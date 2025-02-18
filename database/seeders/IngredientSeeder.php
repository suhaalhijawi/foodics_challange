<?php

namespace Database\Seeders;

use App\Models\Ingredients;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            ['name' => 'Beef', 'total_amount' => 20000, 'current_amount' => 20000],
            ['name' => 'Chicken', 'total_amount' => 20000, 'current_amount' => 20000],
            ['name' => 'Cheese', 'total_amount' => 5000, 'current_amount' => 5000],
            ['name' => 'Onion', 'total_amount' => 1000, 'current_amount' => 1000],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredients::updateOrCreate(['name' => $ingredient['name']], $ingredient);
        }
    }
}
