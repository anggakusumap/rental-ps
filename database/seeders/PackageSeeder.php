<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['name' => '1 Hour Package', 'duration_minutes' => 60, 'price' => 15000, 'description' => 'Perfect for quick gaming session'],
            ['name' => '2 Hour Package', 'duration_minutes' => 120, 'price' => 28000, 'description' => 'Save 2000 with this package'],
            ['name' => '3 Hour Package', 'duration_minutes' => 180, 'price' => 40000, 'description' => 'Save 5000 with this package'],
            ['name' => 'Half Day (4 Hours)', 'duration_minutes' => 240, 'price' => 50000, 'description' => 'Great value for extended play'],
            ['name' => 'Full Day (8 Hours)', 'duration_minutes' => 480, 'price' => 90000, 'description' => 'Best value - all day gaming'],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
