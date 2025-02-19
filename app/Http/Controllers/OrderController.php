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
                [
                    'error_message' => $exception->getMessage()
                ]
            );

           
        }
    }
}
