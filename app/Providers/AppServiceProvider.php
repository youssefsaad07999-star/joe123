<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Observers\OrderObserver;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

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

        Blade::if('active', function ($routeName) {
            return Route::is($routeName);
        });

        Order::observe(OrderObserver::class);

        $this->registerRouteBindings();

        CreateAction::configureUsing(function ($action) {
            return $action->slideOver();
        });

        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole('super_admin') ? true : null;
        });

    }

    private function registerRouteBindings(): void
    {

        Route::bind('gender', function (string $slug) {
            return Category::genders()
                ->active()
                ->where('slug', $slug)
                ->firstOrFail();
        });

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

        Route::bind('user', function ($id) {
            return User::with(['addresses'])->findOrFail($id);
        });
    }
}
