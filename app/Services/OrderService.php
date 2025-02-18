<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Traits\ErrorLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    use ErrorLog;

    /**
     * @param OrderRepository $orderRepository
     * @param IngredientService $ingredientService
     */
    public function __construct
    (
        private IngredientService $ingredientService
    ){}

    /**
     * @param array $data
     * @return Order|null
     * @throws \Throwable
     * @description First create the order and their products after we are going to update ingredients stock then if quantity less or equal than half so send email and return order.
     */
    public function create(array $data): ?Order
    {
        try {
            return DB::transaction(function () use ($data) {
                $order = Order::create($data);
                $order->products()->sync($this->prepareOrderProductsData($data));
                $order->load(['products.ingredients']);
                $this->ingredientService->updateIngredientsStockUsingOrder($order);
                return $order;
            });
        } catch (\Throwable $exception) {
            Log::error($this->message(
                'Service',
                'Order',
                __function__,
                $exception->getMessage()
            ));
            throw $exception;
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws \Throwable
     */
    public function prepareOrderProductsData(array $data): array
    {
        try {
            $response = [];
            foreach($data['products'] as $product) {
                $response[$product['product_id']] = ['quantity' => $product['quantity']];
            }
            return $response;
        } catch (\Throwable $exception) {
            Log::error($this->message(
                'Service',
                'Order',
                __function__,
                $exception->getMessage()
            ));
            throw $exception;
        }
    }
}