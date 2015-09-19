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
    
    private function getDataForEmail($gv) {
        
        // todo Fix Gathering Data for email
        $business_logo = '';//todo get business logo_id from business table
        $m_logo_filename = (is_object($business_logo)) ? 'images/merchant/logos/' . $business_logo->pic . '.' . $business_logo->extension : 'voucher/images/validate_logo.png';
        // get Gift Vouchers Parameter Terms Of Use
        $x = '';//todo get use terms related to voucher parameter of the purchased voucher
        $TermsOfUse = implode(' ● ', $terms);
        //
        //
        $data = array(
            'm_logo_filename' => $m_logo_filename,
            'qr_code' => $gv->qr_code,
            'delivery_date' => $gv->delivery_date,
            'expiry_date' => $gv->expiry_date,
            'voucher_value' => $gv->voucher_value,
            'merchant_business_name' => $gv->parameter->merchant->business_name,
            'voucher_title' => $gv->parameter->Title,
            'TermsOfUse' => $TermsOfUse,
            'merchant_business_address1' => $gv->parameter->merchant->address1,
            'merchant_business_phone' => $gv->parameter->merchant->phone,
            'merchant_business_website' => $gv->parameter->merchant->website,
            'recipient_email' => $gv->recipient_email,
            'customer_name' => $gv->customer->getName(),
            'customer_email' => $gv->customer->user->email,
        );
        //
        return $data;
    }

}
