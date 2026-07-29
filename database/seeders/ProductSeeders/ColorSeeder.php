<?php

namespace Database\Seeders\ProductSeeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            'black' => '#000000',
            'white' => '#FFFFFF',
            'gray' => '#808080',
            'navy' => '#000080',
            'blue' => '#0000FF',
            'red' => '#FF0000',
            'green' => '#008000',
            'olive' => '#808000',
            'brown' => '#A52A2A',
            'beige' => '#F5F5DC',
            'yellow' => '#FFFF00',
            'orange' => '#FFA500',
            'pink' => '#FFC0CB',
            'purple' => '#800080',
            'burgundy' => '#800020',
            'denim' => '#1C3F94',
        ];

        foreach ($colors as $color => $hex_code) {
            Color::factory()->create([
                'name' => $color,
                'hex_code' => $hex_code,
            ]);
        }
    }
}
