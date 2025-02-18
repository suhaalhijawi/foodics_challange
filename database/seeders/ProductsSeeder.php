<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Ingredients;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Product::updateOrCreate(['name' => 'Burger'], ['name' => 'Burger']);

        $beefIngredient = Ingredients::where('name', 'Beef')->first();
        $cheeseIngredient = Ingredients::where('name', 'Cheese')->first();
        $onionIngredient = Ingredients::where('name', 'Onion')->first();

        $product->ingredients()->sync([
            $beefIngredient->id => ['amount' => 150],
            $cheeseIngredient->id => ['amount' => 30],
            $onionIngredient->id => ['amount' => 20],
        ]);

        //Second Product
        $product = Product::updateOrCreate(['name' => 'Chicken Burger'], ['name' => 'Chicken Burger']);

        $fishIngredient = Ingredients::where('name', 'Chicken')->first();

        $product->ingredients()->sync([
            $fishIngredient->id => ['amount' => 150],
            $onionIngredient->id => ['amount' => 20],
        ]);
    }
}
