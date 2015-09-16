<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

//requests
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;

//controllers
use App\Http\Controllers\ApiController;
use App\Http\Controllers\VouchersController;

/**
 * Description of PurchaseController
 *
 * @author mohamed
 */
class PurchaseController extends ApiController{
    
    /**
     * App\Http\Controllers\VouchersController instance
     * @var object
     */
    private $voucher_controller;


    public function __construct( VouchersController $voucher_controller) {
        $this->voucher_controller = $voucher_controller;
    }
    
//    todo OnlinePurchase method
    
    /**
     * Purchase vouchers instore
     * @param PurchaseRequest $request
     */
    public function instorePurchase(PurchaseRequest $request) {
//        todo decisions to make with instorePurchase
        foreach ( $request->get('data') as $purchased_voucher) {
            $purchased_voucher_object = $this->createPurchasedVoucher($purchased_voucher);
//            todo sending virtual voucher according to purchased voucher info
//            todo prepare receipt for all purchased voucher
        }//foreach ( $request->get('data') as $purchased_voucher)
    }
    
    /**
     * Create purchased voucher In Database (vouchers table)
     * @param object $purchased_voucher
     */
    private function createPurchasedVoucher( $purchased_voucher ) {
        $purchased_voucher_to_create = [
          'user_id'=> (int)$purchased_voucher['relations']['user']['data']['user_id']  ,
            'voucher_parameter_id'=>(int)$purchased_voucher['relations']['voucher_parameter']['data']['voucher_parameter_id'],
            'value'=>$purchased_voucher['value'],
            'delivery_date'=>$purchased_voucher['delivery_date'],
            'recipient_email'=>$purchased_voucher['recipient_email'],
            'message'=>$purchased_voucher['message']
        ];
        $purchased_voucher_object = $this->voucher_controller->store($purchased_voucher_to_create);
        return $purchased_voucher_object;
    }

}
