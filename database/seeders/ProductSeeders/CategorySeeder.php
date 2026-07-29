<?php

namespace Database\Seeders\ProductSeeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public static array $data = [
        'men' => [
            'jackets' => ['leather', 'buffers'],
            'shirts' => ['plain', 'formal', 'printed'],
            't-shirts' => ['basic', 'printed', 'stripped'],
            'polo_t-shirts' => ['basic', 'printed'],
            'trousers' => ['chino', 'cargo', 'jeans'],
        ],
        'women' => [
            'jackets' => ['leather', 'buffers'],
            'shirts' => ['plain', 'formal', 'printed'],
            't-shirts' => ['basic', 'printed', 'stripped'],
            'trousers' => ['chino', 'cargo', 'jeans'],
            'dresses' => ['events', 'beach'],
            'skirts' => ['midi', 'mini'],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        foreach (CategorySeeder::$data as $genderName => $categories) {
            $genderOrder = 1;
            $gender = $this->createCategory($genderName, null, 'gender', $genderOrder++);

            $categoryOrder = 1;
            foreach ($categories as $categoryName => $subcategories) {
                $category = $this->createCategory($categoryName, $gender->id, 'category', $categoryOrder++);

                $subcategoryOrder = 1;
                foreach ($subcategories as $subcategoryName) {
                    $this->createCategory($subcategoryName, $category->id, 'subcategory', $subcategoryOrder++);
                }
            }

        }
    }

    private function createCategory(
        string $name,
        ?int $parentId,
        string $depth,
        int $sortOrder = 0
    ): Category {
        return Category::factory()->create([
            'name' => ucfirst(str_replace(['-', '_'], ' ', $name)),
            'slug' => str_replace('_', '-', $name),
            'parent_id' => $parentId,
            'depth' => $depth,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);
    }
}
