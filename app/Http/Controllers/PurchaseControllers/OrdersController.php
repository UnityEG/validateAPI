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
     * tax will come with every order to avoid the problem of changing taxes and old order calculations error
     * @param double $tax tax to be added
     * @return \App\Http\Models\Order Created Order
     */
    public function store($tax=0){
        $order_info_array = [
            'user_id' => (int)JWTAuth::parseToken()->authenticate()->id,
            'number' => $this->generateOrderNumber(),
            'tax' => $tax
        ];
        $created_order_object = Order::create($order_info_array);
        if ( is_object($created_order_object) ) {
            return $created_order_object;
        }//if ( is_object($created_order_object) )
        throw new Exception('Failed to create new order', 500);
    }
    
    /**
     * Generate Order number the largest order number found in database + 1 starting from 1001
     * @return integer
     */
    private function generateOrderNumber(){
        $largest_order_number = max(array_map('intval', Order::lists('number')->toArray()));
        return (1000 > $largest_order_number) ? 1001 : ++$largest_order_number;
    }
}
