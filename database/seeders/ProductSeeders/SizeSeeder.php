<?php

namespace Database\Seeders\ProductSeeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            'S' => 'alpha',
            'M' => 'alpha',
            'L' => 'alpha',
            'XL' => 'alpha',
            '2XL' => 'alpha',
            '28' => 'numeric',
            '29' => 'numeric',
            '30' => 'numeric',
            '31' => 'numeric',
            '32' => 'numeric',
            '33' => 'numeric',
            '34' => 'numeric',
            '35' => 'numeric',
            '36' => 'numeric',
        ];
        $sort_order = 1;
        foreach ($sizes as $name => $type) {

            Size::factory()->create([
                'name' => $name,
                'type' => $type,
                'sort_order' => $sort_order++,
            ]);
        }

    }
}
