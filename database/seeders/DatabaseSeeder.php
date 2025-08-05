<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // User::create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@pos.com',
        //     'password' => bcrypt('password123'),
        //     'role' => 'admin',
        // ]);

        // Category::create(['name' => 'Beverages']);
        // Category::create(['name' => 'Snacks']);
        // Category::create(['name' => 'Dairy']);



        Product::create([
            'name' => 'Milk',
            'category_id' => 3,
            'quantity_available' => 5,
            'price' => 2.50,
            'tax_rate' => 5,
        ]);
        Product::create([
            'name' => 'Chips',
            'category_id' => 2,
            'quantity_available' => 50,
            'price' => 1.50,
        ]);
    }
}
