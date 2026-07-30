<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Observers\OrderObserver;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Livewire::forceAssetInjection();

        Blade::if('active', function ($routeName) {
            return Route::is($routeName);
        });
        Order::observe(OrderObserver::class);
        // Blade::if('admin', function () {
        //     return auth()->check()
        //     && auth()->user()->role === 'admin';
        // });

        $this->registerRouteBindings();
        $this->registerViewComposers();

        CreateAction::configureUsing(function ($action) {
            return $action->slideOver();
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        Artisan::call('storage:link');

    }

    private function registerRouteBindings(): void
    {
        /*'
         * {gender} → Category where depth = 'gender', matched by slug
         */
        Route::bind('gender', function (string $slug) {
            return Category::genders()
                ->active()
                ->where('slug', $slug)
                ->firstOrFail();
        });

        /*
         * {category} → Category where depth = 'category',
         * scoped to the already-resolved {gender}
         */
        Route::bind('category', function (string $slug) {

            if (request()->hasHeader('X-Livewire')) {
                $browserUrl = request()->header('referer');

                // 2. Extract just the path portion if you prefer (e.g., "/men/t-shirt")
                $browserPath = parse_url($browserUrl, PHP_URL_PATH);

                $segments = array_values(array_filter(explode('/', $browserPath)));

                $gender = Category::genders()->active()->where('slug', $segments[0] ?? null)->first();

            } else {
                $gender = request()->route('gender');
            }

            if (! $gender || ! isset($gender->id)) {
                return null;
            }

            return Category::categories()
                ->active()
                ->where('slug', $slug)
                ->where('parent_id', $gender->id)
                ->first();
        });

        /*
         * {subcategory} → Category where depth = 'subcategory',
         * scoped to the already-resolved {category}
         */
        Route::bind('subcategory', function ($slug) {

            if (request()->hasHeader('X-Livewire')) {
                $browserUrl = request()->header('referer');

                $browserPath = parse_url($browserUrl, PHP_URL_PATH);

                $segments = array_values(array_filter(explode('/', $browserPath)));

                $category = Category::categories()->active()
                    ->whereHas('parent', function ($query) use ($segments) {
                        $query->where('depth', 'gender')->where('slug', $segments[0] ?? null);
                    })
                    ->where('slug', $segments[1] ?? null)
                    ->first();
            } else {
                $category = request()->route('category');
            }

            if (! $category || ! isset($category->id)) {
                return null;
            }

            return Category::subcategories()
                ->active()
                ->where('slug', $slug)
                ->where('parent_id', $category->id)
                ->firstOrFail();
        });

        Route::bind('adminCategory', function (string $id) {
            return Category::findOrFail($id);
        });

        Route::bind('product', function ($slug) {
            return Product::with(['variants.size', 'variants.color', 'images', 'primaryImage', 'fit', 'brand'])
                ->where('slug', $slug)
                ->first();
        });

        Route::bind('order', function ($id) {
            return Order::with([
                'variants.product.fit',
                'variants.product.images',
                'variants.size',
                'variants.color',
                'address',
                'payment',
                'user',
            ])->findOrFail($id);

        });
        // orders addresses
        Route::bind('user', function ($id) {
            return User::with(['addresses'])->findOrFail($id);
        });
    }

    private function registerViewComposers(): void
    {
        View::composer('components.admin.nav', function ($view) {
            $view->with('pendingCount', Order::where('status', 'pending')->count());
        });

        View::composer('components.layout.layout', function ($view) {
            $query = CartItem::query();

            $cartItems = auth()->check()
            ? $query->forUser(auth()->id())->with('variant.product.images')->get()
            : $query->forSession(session()->getId())->with('variant.product.images')->get();

            $cartTotal = $cartItems->sum->line_total;

            $view->with(compact('cartItems', 'cartTotal'));
        });

        View::composer('components.layout.nav', function ($view) {

            $genders = Category::genders()
                ->active()
                ->with([
                    'children' => function ($query) {
                        $query->active()->with([
                            'children' => function ($subQuery) {
                                $subQuery->active();
                            },
                        ]);
                    },
                ])
                ->get();

            $view->with('navGenders', $genders);

        });
    }
}
