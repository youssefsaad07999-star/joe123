<?php

namespace Database\Seeders;

use Database\Seeders\ProductSeeders\CategorySeeder;
use Database\Seeders\ProductSeeders\ColorSeeder;
use Database\Seeders\ProductSeeders\FitSeeder;
use Database\Seeders\ProductSeeders\SizeSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            FitSeeder::class,
            ColorSeeder::class,
            SizeSeeder::class,
            SuperAdminSeeder::class,
            ShippingMethodSeeder::class,
            ShopSettingsSeeder::class,
            CountrySeeder::class,
        ]);
    }
}
