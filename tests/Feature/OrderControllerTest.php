<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Statuses;
use App\Mail\LowStockAlert;
use App\Models\Ingredients;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    // use RefreshDatabase;


    public function test_order_is_correctly_stored_and_stock_updated()
    {
        Mail::fake();
        Statuses::firstOrCreate(
            ['code' => 'pd'],
            [
                'name' => 'pending',
                'code' => 'pd',
                'is_active' => 1
            ]
        );
        $user = User::firstOrCreate(
            ['email' => 'suhakhijawi@gmail.com'],
            [
                'email' => 'suhakhijawi@gmail.com',
                'name' => 'Suha Hijawi',
                'password' => 'suhakhijawi'
            ]
        );

        // Seed database
        $ingredient1 = Ingredients::firstOrCreate(['name' => 'Beef'], ['name' => 'Beef', 'total_amount' => 20000, 'current_amount' => 20000]);
        $ingredient2 = Ingredients::firstOrCreate(['name' => 'Cheese'], ['name' => 'Cheese', 'total_amount' => 5000, 'current_amount' => 5000]);
        $ingredient3 = Ingredients::firstOrCreate(['name' => 'Onion'], ['name' => 'Onion', 'total_amount' => 1000, 'current_amount' => 1000]);

        $product = Product::create(['name' => 'Burger']);
        $product->ingredients()->attach([
            $ingredient1->id => ['amount' => 150, 'ingredient_id' => $ingredient1->id],
            $ingredient2->id => ['amount' => 30, 'ingredient_id' => $ingredient2->id],
            $ingredient3->id => ['amount' => 20, 'ingredient_id' => $ingredient3->id],
        ]);

        // Send order request
        $response = $this->postJson('orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 4]
            ],
            'user_id' => $user->id,
        ]);

        $response->assertStatus(Response::HTTP_OK);

    }
}
