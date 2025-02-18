<?php

namespace App\Services;

use App\Models\Order;
use App\Traits\ErrorLog;
use App\Mail\LowStockAlert;
use App\Models\Ingredients;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IngredientService
{
    use ErrorLog;


    /**
     * @param Order $order
     * @return void
     * @throws \Throwable
     * @description Update Each ingredient price according to order product quantity
     */
    public function updateIngredientsStockUsingOrder(Order $order): void
    {
        try {
            (new Collection());
            $order->products->each(function ($product) {
                $product->ingredients->each(function ($ingredient) use ($product) {
                    $current_amount = $ingredient->current_amount - ($ingredient->pivot->amount * $product->pivot->quantity);
                    $totalConsumption = $ingredient->pivot->amount * $product->pivot->quantity;

                    if ($current_amount < 0) {
                        throw new \Exception("{$ingredient->name} is {$current_amount}  out of stock.");
                    }

                    $ingredient->decrement('current_amount', $totalConsumption);

                    // Check for low stock alert (50% threshold)
                    $this->checkAndSendLowStockAlert($ingredient);
                });
            });
        } catch (\Throwable $exception) {
            Log::error($this->message(
                'Service',
                'Ingredient',
                __function__,
                $exception->getMessage()
            ));
            throw $exception;
        }
    }
    private function checkAndSendLowStockAlert(Ingredients $ingredient)
    {
        $threshold = $ingredient->total_amount * 0.5;

        if ($ingredient->current_amount <= $threshold && $ingredient->is_alert_email_sent == 0) {

            try {
                // Ensure email is sent if stock is below 50%
                Mail::to('suha.hijaw.sh.93@gmail.com')->send(new LowStockAlert($ingredient));
                $ingredient->is_alert_email_sent = 1;
                $ingredient->save();
            } catch (\Throwable $exception) {

                Log::info($exception->getMessage());
            }
        }
    }
}
