<?php

namespace Database\Seeders\Package;

use App\Models\Package\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter Plan',
                'description' => 'Perfect for individuals and personal use',
                'price' => 250.00
            ],
            [
                'name' => 'Team Plan',
                'description' => 'For small teams with shared access',
                'price' => 950.00
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'Advanced features for large businesses',
                'price' => 2500.00
            ]
        ];

        foreach ($packages as $item) {
            $package = Package::create([
                'name' => $item['name'],
                'description' => $item['description'],
                'status' => 'active',
            ]);

            $package->prices()->create([
                'currency' => 'PHP',
                'amount' => $item['price'],
                'is_active' => true,
            ]);
        }
    }
}
