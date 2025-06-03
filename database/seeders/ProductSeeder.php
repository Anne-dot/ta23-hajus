<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->create(['quantity' => 1]);
        Product::factory()->create(['quantity' => 3]);
        Product::factory()->create(['quantity' => 4]);
        Product::factory(9)->create();
    }
}
