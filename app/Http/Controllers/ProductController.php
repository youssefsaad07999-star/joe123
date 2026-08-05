<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ShopSetting;

class ProductController extends Controller
{
    public function home()
    {
        $genders = Category::genders()->active()->orderBy('sort_order')->get();

        $landingImage = ShopSetting::where('key', 'landing_hero_image')->value('value');

        $freeShippingThreshold = ShopSetting::get('free_shipping_threshold');

        return view('welcome', compact(['genders', 'landingImage', 'freeShippingThreshold']));
    }

    public function genderIndex(Category $gender)
    {
        // 2. Flatten all subcategory IDs safely in memory
        $subcategoryIds = $gender->children
            ->flatMap(fn ($category) => $category->children)
            ->pluck('id');

        $products = Product::with(['variants', 'images', 'category.parent.parent'])
            ->isActive()
            ->whereIn('category_id', $subcategoryIds)
            ->latest()
            ->paginate(16);

        return view('product.gender.index', compact('gender', 'products'));
    }

    public function categoryShow(Category $gender, Category $category)
    {
        $subcategories = $category->children;

        $products = Product::with(['variants.color', 'variants.size', 'images', 'category.parent'])
            ->isActive()
            ->whereIn('category_id', $subcategories->pluck('id'))
            ->latest()
            ->paginate(16);

        return view('product.category.show', compact('gender', 'subcategories', 'category', 'products'));

    }

    public function subcategoryShow(Category $gender, Category $category, Category $subcategory)
    {
        $subcategories = $category->children;

        $products = Product::query()
            ->isActive()
            ->where('category_id', $subcategory->id)
            ->with([
                'variants.color',
                'variants.size',
                'images',
            ])
            ->latest()
            ->paginate(16);

        return view('product.subcategory.show', compact('gender', 'category', 'products', 'subcategory', 'subcategories'));
    }

    public function productShow(Product $product)
    {
        $subcategory = $product->category;
        $category = $subcategory?->parent;
        $gender = $category?->parent;

        return view('product.show', compact('gender', 'category', 'subcategory', 'product'));
    }

    public function byCategory(Category $category)
    {

        $products = Product::inCategory($category)
            ->with('variants')
            ->get();

        // dd($products[0]->variants[0]->sku);

        // return view('products.index', compact('products', 'category'));
    }
}
