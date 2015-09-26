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
    
    Route::get('VoucherParameters/title/{voucher_title}', [
        'uses' => 'VoucherParametersController@searchByVoucherTitle',
        'as' => 'VoucherParameter.searchByVoucherTitle'
    ]);
    
    Route::get('VoucherParameters/business-name/{business_name}', [
        'uses' => 'VoucherParametersController@searchByBusinessName',
        'as' => 'VoucherParameters.searchByBusinessName'
    ]);
    
    Route::post('VoucherParameters/storeDealVoucherParameters', [
        'uses' => 'VoucherParametersController@storeDealVoucherParameters',
        'as' => 'VoucherParameters.storeDealVoucherParameters'
    ]);
    
    Route::post('VoucherParameters/storeGiftVoucherParameters', [
        'uses' => 'VoucherParametersController@storeGiftVoucherParameters',
        'as' => 'VoucherParameters.storeGiftVoucherParameters'
    ]);
    
    
//    VoucherImages Routes
    Route::get('VoucherImages', [
        'uses' => 'VoucherImagesController@index',
        'as' => 'VoucherImages.index'
    ]);
    
    Route::get('VoucherImages/showGiftImages', [
        'uses' => 'VoucherImagesController@showGiftImages',
        'as' => 'VoucherImages.showGiftImages'
    ]);
    
    Route::get('VoucherImages/showDealImages', [
        'uses' => 'VoucherImagesController@showDealImages',
        'as' => 'VoucherImages.showDealImages'
    ]);
    
    Route::post('VoucherImages/storeGiftImage', [
        'uses' => 'VoucherImagesController@storeGiftImage',
        'as' => 'VoucherImages.storeGiftImage'
    ]);
    
    Route::post('VoucherImages/storeDealImage', [
        'uses' => 'VoucherImagesController@storeDealImage' ,
        'as' => 'VoucherImages.storeDealImage' 
    ]);
    
    Route::delete('VoucherImages/{voucher_image_id}', [
        'uses' => 'VoucherImagesController@destroy',
        'as' => 'VoucherImages.destroy'
    ]);
    
    
    
//    Purchase Routes
    
    Route::post('Purchase/onlinePurchase', [
        'uses' => 'PurchaseController@onlinePurchase',
        'as' => 'Purchase.onlinePurchase'
    ]);
    Route::post('Purchase/instorePurchase', array(
        'uses' => 'PurchaseController@instorePurchase',
        'as' => 'Purchase.instorePurchase'
    ));
    
//    VoucherValidationLog Routes
    Route::post('VoucherValidationLog/validateVoucher', [
        'uses' => 'VoucherValidationLogController@validateVoucher',
        'as' => 'VoucherValidationLog.validate'
    ]);
    
    Route::get('VoucherValidationLog/getAllLogs/{voucher_id}', [
        'uses' => 'VoucherValidationLogController@getAllLogs',
        'as' => 'VoucherValidationLog.getAllLogs'
    ]);
    
    Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
    Route::post('authenticate', 'AuthenticateController@authenticate');
});
