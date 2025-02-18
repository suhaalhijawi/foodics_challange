<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Statuses;
use App\Traits\ErrorLog;
use App\Mail\LowStockAlert;
use App\Models\Ingredients;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use ErrorLog;

    /**
     * @param OrderService $orderService
     */
    public function __construct(private OrderService $orderService)
    {

    }

    /**
    * @param OrderRequest $orderRequest
    */
    public function store(OrderRequest $orderRequest)
    {
        try {
            /**
             * @description Get the authenticated user and merge it into order payload
             */
            $orderData = $orderRequest->validated();
            $orderData['user_id'] = User::first()->id;
            $orderData['status_id']  = Statuses::findByCode('pd')->id;
            $orderData['dispatched_at'] = Carbon::now();

            return response()->success(
                Response::HTTP_OK,
                trans('messages.order_has_been_created'),
                [
                    'orderData' => $this->orderService->create($orderData),
                
                ]
            );
            
        } catch (\Throwable $exception) {
            Log::error($this->message(
                'Controller',
                'Order',
                __function__,
                $exception->getMessage()
            ));

            return response()->errors(
                Response::HTTP_BAD_REQUEST,
                $exception->getMessage(),
                []
            );

           
        }
    }
    public function store2(Request $request)
    {

        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => User::first()->id,
            'status_id' => Statuses::findByCode('pd')->id,
            'dispatched_at' => Carbon::now()
        ]);

        foreach ($request->products as $productData) {
            $product = Product::findOrFail($productData['product_id']);
            $quantity = $productData['quantity'];

            // Attach products to order
            $order->products()->attach($product->id, ['quantity' => $quantity]);

            // Update ingredient stock
            foreach ($product->ingredients as $ingredient) {
                $totalConsumption = $ingredient->pivot->amount * $quantity;
                $ingredient->decrement('current_amount', $totalConsumption);

                // Check for low stock alert (50% threshold)
                $this->checkAndSendLowStockAlert($ingredient);
            }
        }

        return response()->json(['message' => 'Order placed successfully', 'order_id' => $order->id], 201);
    }
    private function checkAndSendLowStockAlert(Ingredients $ingredient)
    {
        $threshold = $ingredient->total_amount * 0.5;

        if ($ingredient->current_amount <= $threshold && !$ingredient->is_alert_email_sent) {

            try {
                // Ensure email is sent if stock is below 50%
                Mail::to('suha.hijaw.sh.93@gmail.com')->send(new LowStockAlert($ingredient));

                $ingredient->is_alert_email_sent = true;
                $ingredient->save();
            } catch (\Throwable $exception) {

                Log::info($exception->getMessage());

            }
        }
    }
}
