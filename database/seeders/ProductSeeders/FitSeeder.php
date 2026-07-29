<?php

namespace Database\Seeders\ProductSeeders;

use App\Models\Fit;
use Illuminate\Database\Seeder;

class FitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fits = ['slim', 'regular', 'oversized', 'skinny', 'straight', 'relaxed', 'tapered', 'bootcut', 'wide-leg', 'boxy', 'cropped', 'baggy', 'super-baggy'];

        foreach ($fits as $fit) {
            Fit::factory()->create([
                'name' => $fit,
            ]);
        }
    }
}
