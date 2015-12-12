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
//    todo prevent showing laravel welcome screen and redirect to UserInterface
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
    Route::resource('lessons', 'LessonsController');
    Route::resource('GiftVouchers', 'GiftVouchersController');
    Route::resource('GiftVoucherValidation', 'GiftVoucherValidationController');
    Route::resource('UserFeedback', 'UserFeedbackController');
    
//    Users Routes
    
    Route::get('Users/searchUserByName/{username}', [
        'uses' => 'UsersControllers\UsersController@searchUserByName',
        'as' => 'UsersController.searchUserByName'
    ]);
    
    Route::resource('Users', 'UsersControllers\UsersController');
    
    // login, logout and authentication
        Route::get('authenticate', 'AuthenticateController@index');

        Route::get('logout', [
            'uses' => 'UsersControllers\AuthenticateController@logout',
            'as' => 'Authenticate.logout'
        ]);

        Route::post('authenticate', [
            'uses' => 'UsersControllers\AuthenticateController@authenticate',
            'as' => 'Authenticate.authenticate'
        ]);
        
        Route::post('Authenticate/facebook', [
            'uses' => 'UsersControllers\AuthenticateController@facebook',
            'as' => 'Authenticate.facebook'
        ]);
    
    // UserGroups
        Route::resource('UserGroups', 'UsersControllers\UserGroupsController');
    
//    Business Routes
    Route::get('Business/listPartners', [
        'uses' => 'BusinessController@listPartners',
        'as' => 'Business.listPartners'
    ]);
    
    Route::get('Business/listFeatured', [
        'uses' => 'BusinessController@listFeatured',
        'as' => 'Business.listFeatured'
    ]);
    
    Route::get('Business/listCreateRequest', [
        'uses' => 'BusinessController@listCreateRequest',
        'as' => 'Business.listCreateRequest'
    ]);
    
    Route::get('Business/showDisplayBusiness/{id}', [
        'uses' => 'BusinessController@showDisplayBusiness',
        'as' => 'BusinessController.showDisplayBusiness'
    ]);
    
    Route::get('Business/acceptCreateRequest/{id}', [
        'uses' => 'BusinessController@acceptCreateRequest',
        'as' => 'BusinessController.acceptCreateRequest'
    ]);
    
    Route::resource('Business', 'BusinessController');
    
    //    BusinessLogos
        Route::resource('BusinessLogos', 'BusinessControllers\BusinessLogosController');
    //      BusinessTypes
        Route::get('BusinessTypes', [
            'uses' => 'BusinessControllers\BusinessTypesController@index',
            'as' => 'BusinessTypes.index'
        ]);
        
//  Vouchers Routes
        
    //    VoucherParameters Routes
        Route::get('VoucherParameters', [
            'uses' => 'VouchersControllers\VoucherParametersController@index',
            'as' => 'VoucherParameters.index'
        ]);

        Route::get('VoucherParameters/listAllActiveVouchersParameters', [
            'uses' => 'VouchersControllers\VoucherParametersController@listAllActiveVouchersParameters',
            'as' => 'VoucherParameters.listAllActiveVouchersParameters'
        ]);
        
        Route::get('VoucherParameters/listActiveVoucherParametersForBusiness/{business_id}', [
            'uses' => 'VouchersControllers\VoucherParametersController@listActiveVoucherParametersForBusiness',
            'as' => 'VoucherParameters.listActiveVoucherParametersForBusiness'
        ]);
        
        Route::get('VoucherParameters/listVoucherParametersTypes', [
            'uses' => 'VouchersControllers\VoucherParametersController@listVoucherParametersTypes',
            'as' => 'VoucherParameters.listVoucherParametersTypes'
        ]);

        Route::get('VoucherParameters/searchByVoucherTitle/{voucher_title}', [
            'uses' => 'VouchersControllers\VoucherParametersController@searchByVoucherTitle',
            'as' => 'VoucherParameter.searchByVoucherTitle'
        ]);

        Route::get('VoucherParameters/searchByBusinessName/{business_name}', [
            'uses' => 'VouchersControllers\VoucherParametersController@searchByBusinessName',
            'as' => 'VoucherParameters.searchByBusinessName'
        ]);
        
        Route::get('VoucherParameters/searchByBusinessId/{business_id}', [
            'uses' => 'VouchersControllers\VoucherParametersController@searchByBusinessId',
            'as' => 'VoucherParameters.searchByBusinessId'
        ]);
        
        Route::get('VoucherParameters/listGiftVoucherParametersOfBusiness/{business_id}', [
            'uses' => 'VouchersControllers\VoucherParametersController@listGiftVoucherParametersOfBusiness',
            'as' => 'VoucherParameters.listGiftVoucherParametersOfBusiness'
        ]);
        
        Route::get('VoucherParameters/listDealVoucherParametersOfBusiness/{business_id}', [
            'uses' => 'VouchersControllers\VoucherParametersController@listDealVoucherParametersOfBusiness',
            'as' => 'VoucherParameters.listDealVoucherParametersOfBusiness'
        ]);
        
        Route::get('VoucherParameters/show/{voucher_parameter_id}', [
            'uses' => 'VouchersControllers\VoucherParametersController@show',
            'as' => 'VoucherParameters.show'
        ]);
        
        Route::get('VoucherParameters/showActiveVoucherParameter/{voucher_parameter_id}', [
            'uses' => 'VouchersControllers\VoucherParametersController@showActiveVoucherParameter',
            'as' => 'VoucherParameters.showActiveVouecherParameter'
        ]);

        Route::post('VoucherParameters/storeDealVoucherParameters', [
            'uses' => 'VouchersControllers\VoucherParametersController@storeDealVoucherParameters',
            'as' => 'VoucherParameters.storeDealVoucherParameters'
        ]);

        Route::post('VoucherParameters/storeGiftVoucherParameters', [
            'uses' => 'VouchersControllers\VoucherParametersController@storeGiftVoucherParameters',
            'as' => 'VoucherParameters.storeGiftVoucherParameters'
        ]);

        Route::patch('VoucherParameters/updateGiftVoucherParameters',[
            'uses' => 'VouchersControllers\VoucherParametersController@updateGiftVoucherParameters',
            'as' => 'VoucherParameters.updateGiftVoucherParameters'
        ]);

        Route::patch('VoucherParameters/updateDealVoucherParameters', [
            'uses' => 'VouchersControllers\VoucherParametersController@updateDealVoucherParameters',
            'as' => 'VoucherParameters.updateDealVoucherParameters'
        ]);


    //    VoucherImages Routes
        Route::get('VoucherImages', [
            'uses' => 'VoucherImagesController@index',
            'as' => 'VoucherImages.index'
        ]);

        Route::get('VoucherImages/listGiftImages', [
            'uses' => 'VoucherImagesController@listGiftImages',
            'as' => 'VoucherImages.listGiftImages'
        ]);

        Route::get('VoucherImages/listDealImages', [
            'uses' => 'VoucherImagesController@listDealImages',
            'as' => 'VoucherImages.listDealImages'
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
        
    //  UseTerms
        Route::get('UseTerms', [
            'uses' => 'VouchersControllers\UseTermsController@index',
            'as' => 'UseTerms.index'
        ]);
        
    //  Vouchers
        Route::get('Vouchers', [
            'uses' => 'VouchersControllers\VouchersController@index',
            'as' => 'Vouchers.index'
        ]);
        
        Route::get('Vouchers/{id}', [
            'uses' => 'VouchersControllers\VouchersController@show',
            'as' => 'Vouchers.show'
        ]);
        
        Route::get('Vouchers/listAllVouchersPurchasedByCustomer/{customer_id}', [
            'uses' => 'VouchersControllers\VouchersController@listAllVouchersPurchasedByCustomer',
            'as' => 'Vouchers.listAllVouchersPurchasedByCustomer'
        ]);
        
    //  VirtualVouchers
        Route::get('VirtualVouchers/showVirtualVoucherImage/{code}', [
            'uses' => 'VouchersControllers\VirtualVouchersController@showVirtualVoucherImage',
            'as' => 'VirtualVouchers.showVirtualVoucherImage'
        ]);
        
    //    VoucherValidationLog Routes
        Route::get('VoucherValidationLogs/getAllLogs/{voucher_id}', [
            'uses' => 'VouchersControllers\VoucherValidationLogsController@getAllLogs',
            'as' => 'VoucherValidationLogs.getAllLogs'
        ]);
        
        Route::get('VoucherValidationLogs/{voucher_validation_log_id}', [
            'uses' => 'VouchersControllers\VoucherValidationLogsController@show',
            'as' => 'VoucherValidationLogs.show'
        ]);
        
        Route::post('VoucherValidationLogs/validateVoucher', [
            'uses' => 'VouchersControllers\VoucherValidationLogsController@validateVoucher',
            'as' => 'VoucherValidationLogs.validateVoucher'
        ]);
        
        Route::post('VoucherValidationLogs/checkVoucher', [
            'uses' => 'VouchersControllers\VoucherValidationLogsController@checkVoucher',
            'as' => 'VoucherValidationLogs.checkVoucher'
        ]);

//    Purchase Routes
    Route::post('Purchase/onlinePurchase', [
        'uses' => 'PurchaseControllers\PurchaseController@onlinePurchase',
        'as' => 'Purchase.onlinePurchase'
    ]);
    
    Route::post('Purchase/instorePurchase', array(
        'uses' => 'PurchaseControllers\PurchaseController@instorePurchase',
        'as' => 'Purchase.instorePurchase'
    ));
    
//    Regions
    Route::get('Regions', [
        'uses' => 'RegionsController@index',
        'as' => 'Regions.index'
    ]);
    
    Route::get('Regions/renderHtmlCollection', [
        'uses' => 'RegionsController@renderHtmlCollection',
        'as' => 'Regions.renderHtmlCollection'
    ]);
    
//    Postcodes
    Route::get('Postcodes', [
        'uses' => 'PostcodesController@index',
        'as' => 'Postcode.index'
    ]);
    
//    Towns
    Route::get('Towns', [
        'uses' => 'TownsController@index',
        'as' => 'Towns.index'
    ]);
    
//    ContactUs
    Route::post('ContactUs', [
        'uses' => 'ContactUsController@contactUs',
        'as' => 'ContactUs.contactUs'
    ]);
});
