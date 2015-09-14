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

/**
 * Description of PurchaseController
 *
 * @author mohamed
 */
class PurchaseController extends ApiController{
//    todo validate customer input credentials
//    todo OnlinePurchase method
    public function instorePurchase(PurchaseRequest $request) {
        dd($request->all());
    }
//    todo Create purchased voucher method
}
