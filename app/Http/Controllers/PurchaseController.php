<?php

namespace App\Http\Controllers;

use App\aaa\g;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\VouchersController;
use App\Http\Models\Voucher;
use App\Http\Requests\PurchaseRequest;

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
            $this->generateVirtualVoucher($purchased_voucher_object);
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
    
    private function generateVirtualVoucher(Voucher $purchased_voucher_object) {
        
        // Gathering Data for email
        $data = $this->getDataForEmail($purchased_voucher_object);
        
        // Generate virtual voucher image 
        $voucher_filename = g::voucher($data);
        //
        // Add $voucher_filename to $data array
        $data['voucher_filename'] = $voucher_filename;
        //
        dd($data);
        return $data;
    }
    
    private function getDataForEmail(Voucher $purchased_voucher_object) {
        
        $business_logo_object = $purchased_voucher_object->voucherParameter->business->getActiveLogo();
//        todo add folders in assets to hold both merchant logos and voucher_default logo
        $business_logo_filename = (is_object($business_logo_object)) ? 'images/merchant/logos/' . $business_logo_object->name . '.' . $business_logo_object->extension : 'voucher/images/validate_logo.png';
        // get Gift Vouchers Parameter Terms Of Use
        $terms_of_use_objects = $purchased_voucher_object->voucherParameter->useTerms()->get(['name'])->toArray();//todo get use terms related to voucher parameter of the purchased voucher
        $terms_of_use = implode(' ● ', array_pluck($terms_of_use_objects, 'name'));
        //
        $data = array(
            'm_logo_filename' => $business_logo_filename,
            'qr_code' => $purchased_voucher_object->code,
            'delivery_date' => $purchased_voucher_object->delivery_date,
            'expiry_date' => $purchased_voucher_object->expiry_date,
            'voucher_value' => $purchased_voucher_object->value,
            'merchant_business_name' => $purchased_voucher_object->voucherParameter->business->business_name,
            'voucher_title' => $purchased_voucher_object->voucherParameter->title,
            'TermsOfUse' => $terms_of_use,
            'merchant_business_address1' => $purchased_voucher_object->voucherParameter->business->address1,
            'merchant_business_phone' => $purchased_voucher_object->voucherParameter->business->phone,
            'merchant_business_website' => $purchased_voucher_object->voucherParameter->business->website,
            'recipient_email' => $purchased_voucher_object->recipient_email,
            'customer_name' => $purchased_voucher_object->user->getName(),
            'customer_email' => $purchased_voucher_object->user->email,
        );
        //
        return $data;
    }

}
