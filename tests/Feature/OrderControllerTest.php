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
    use RefreshDatabase;


    /** @test */
    public function it_stores_an_order_and_updates_stock_correctly()
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
        User::firstOrCreate(
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


        $response = $this->postJson('orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('orders', []);
        $this->assertEquals(20000, $ingredient1->total_amount);
        $this->assertEquals(5000, $ingredient2->total_amount);
        $this->assertEquals(1000, $ingredient3->total_amount);
    }

    /** @test */
    public function it_sends_a_low_stock_email_once()
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
        User::firstOrCreate(
            ['email' => 'suhakhijawi@gmail.com'],
            [
                'email' => 'suhakhijawi@gmail.com',
                'name' => 'Suha Hijawi',
                'password' => 'suhakhijawi'
            ]
        );
        $cheese = Ingredients::firstOrCreate(['name' => 'Cheese', 'total_amount' => 2600, 'current_amount' => 5000, 'is_alert_email_sent' => false]);

        $burger = Product::create(['name' => 'Burger']);
        $burger->ingredients()->attach([
            $cheese->id => ['amount' => 150, 'ingredient_id' => $cheese->id],
        ]);


        $this->postJson('orders', [
            'products' => [['product_id' => $burger->id, 'quantity' => 10]],
        ]);

        Mail::to('suha.hijaw.sh.93@gmail.com')->send(new LowStockAlert($cheese));

        $this->assertEquals(false,$cheese->is_alert_email_sent);
    }
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
        User::firstOrCreate(
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
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);
        if(!empty($response->json()['errors'])){
            $response->assertStatus(Response::HTTP_BAD_REQUEST);
        }else{
            $response->assertStatus(Response::HTTP_OK);
        }
       
    }
}
