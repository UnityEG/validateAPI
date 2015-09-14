<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'v1'], function() {
    
// Find merchant gift vouchers by merchant user id
    Route::get('Merchants/{id}/GiftVouchers', array(
        'uses' => 'GiftVouchersController@findByMerchant',
        'as' => 'GiftVouchers.findByMerchant'
    ));
    
// Find merchant gift vouchers by its status id
    Route::get('Merchants/{id}/GiftVouchers/Status/{StatusId}', array(
        'uses' => 'GiftVouchersController@findByMerchantAndStatus',
        'as' => 'GiftVouchers.findByMerchantAndStatus'
    ));
    
// Check gift voucher by its code
    Route::get('GiftVouchers/check/{GiftVoucherCode}', array(
        'uses' => 'GiftVouchersController@check',
        'as' => 'GiftVouchers.check'
    ));
    
// RESTful Routers
    Route::resource('users', 'UsersController');
    Route::resource('lessons', 'LessonsController');
    Route::resource('GiftVouchers', 'GiftVouchersController');
    Route::resource('GiftVoucherValidation', 'GiftVoucherValidationController');
    Route::resource('UserFeedback', 'UserFeedbackController');
    
//    VoucherParameters Routes
    Route::get('VoucherParameters/{voucher_type}/all', [
        'uses' => 'VoucherParametersController@index'
    ]);
    Route::resource('VoucherParameters', 'VoucherParametersController');
    
//    Purchase Routes
    Route::post('Purchase/instorePurchase', array(
        'uses' => 'PurchaseController@instorePurchase',
        'as' => 'Purchase.instorePurchase'
    ));
    
    Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
    Route::post('authenticate', 'AuthenticateController@authenticate');
});
