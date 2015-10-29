<?php

namespace App\Http\Controllers\PurchaseControllers;

use App\Http\Controllers\ApiController;
use App\Http\Models\Order;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrdersController extends ApiController
{
    /**
     * Store new Order
     */
    public function store(){
        $order_info_array = [
            'user_id' => (int)JWTAuth::parseToken()->authenticate()->id,
//            todo create method to generate order number
            'number' => 12345,
//            todo find out where to get tax
            'tax' => ''
        ];
        $created_order_object = Order::create($order_info_array);
        if ( is_object($created_order_object) ) {
            return $created_order_object;
        }
        throw new Exception('Failed to create new order', 500);
    }
}
