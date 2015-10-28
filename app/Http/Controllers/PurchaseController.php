<?php

namespace App\Http\Controllers;

use App\EssentialEntities\GeneralHelperTools;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\VouchersController;
use App\Http\Models\Voucher;
use App\Http\Requests\PurchaseRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

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
//        todo apply JWTAuth middleware on all methods in this controller
        $this->voucher_controller = $voucher_controller;
    }
    
    /**
     * Purchase vouchers online
     * @param PurchaseRequest $request
     */
    public function onlinePurchase(PurchaseRequest $request ) {
//        todo go with the request to the payment gateway and wait for the response
//        todo create order and get order_id to put it with each purchased voucher
        foreach ( $request->get('data') as $purchased_voucher) {
            $purchased_voucher_object = $this->createPurchasedVoucher($purchased_voucher);
            $this->sendVirtualVoucherMail($purchased_voucher_object);
            $receipt_data[] =$this->vouchersReceipt($purchased_voucher_object);
            $total_value = (!isset($total_value))? $purchased_voucher_object->value : $total_value + $purchased_voucher_object->value;
        }//foreach ( $request->get('data') as $purchased_voucher)
        $this->sendReceiptMailToCustomer($receipt_data, $total_value);
    }
    
    /**
     * Purchase vouchers instore
     * @param PurchaseRequest $request
     */
    public function instorePurchase(PurchaseRequest $request) {
//        todo decisions to make with instorePurchase
        
    }
    
    /**
     * Create purchased voucher In Database (vouchers table)
     * @param object $purchased_voucher
     */
    private function createPurchasedVoucher( $purchased_voucher ) {
        $purchased_voucher_to_create = [
          'user_id'=> (int)  JWTAuth::parseToken()->authenticate()->id  ,
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
        return $data;
    }
    
    private function getDataForEmail(Voucher $purchased_voucher_object) {
        
        $business_logo_object = $purchased_voucher_object->voucherParameter->business->getActiveLogo();
        $business_logo_filename = (is_object($business_logo_object)) ? 'images/merchant/logos/' . $business_logo_object->name . '.' . $business_logo_object->extension : 'voucher/images/validate_logo.png';
        // get Gift Vouchers Parameter Terms Of Use
        $terms_of_use_objects = $purchased_voucher_object->voucherParameter->useTerms()->get(['name'])->toArray();
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
    
    
    private function sendVirtualVoucherMail(Voucher $purchased_voucher_object, $MailBodyView='email.vouchers.virtualVoucher') {
        //
        // Generate Virtual Voucher
        $data = $this->generateVirtualVoucher($purchased_voucher_object);
        //
        
            $data['email_to_email'] = (!is_null($data['recipient_email'])) ? $data['recipient_email'] : $data['customer_email'];
            $data['email_to_name'] = (!is_null($data['recipient_email'])) ? $data['recipient_email'] : $data['customer_name'];
        
//        echo '<pre>';
//        dd($data);
        // extract $data array as variables
        extract($data);
        // 
        if (ini_get('max_execution_time') < 180) {
            ini_set('max_execution_time', 180);
        }
        // Send Mail with virtual voucher image attached
        \Mail::queue($MailBodyView, $data, function($message) use ($data, $voucher_filename) {
            //
            $message->to($data['email_to_email'], $data['email_to_name'])->subject('Validate Voucher');
            //$message->to('shadymag@gmail.com', 'customer_name')->subject('Voucher Purchased'); // for Testing
            $message->attach($voucher_filename);
        });
        // 
        // For security delete virtual voucher file after use it
        $this->unlinkVirtualVoucher($voucher_filename);
    }
    
    private function unlinkVirtualVoucher($voucher_filename) {
        //
        // For security delete virtual voucher file after use it
        if (file_exists($voucher_filename)) {
            unlink($voucher_filename);
        }
    }
    
    /**
     * Prepare receipt data
     * @param Voucher $purchased_voucher_object
     * @return array
     */
    public function vouchersReceipt(  Voucher $purchased_voucher_object ) {
        return [
            'voucher_title' => $purchased_voucher_object->voucherParameter->title,
            'voucher_value' => g::formatCurrency($purchased_voucher_object->value),
            'recipient_email' => $purchased_voucher_object->recipient_email ,
            'delivery_date' => g::formatDate($purchased_voucher_object->delivery_date),
            'expiry_date' => g::formatDate($purchased_voucher_object->expiry_date)
        ];
        
    }
    
    /**
     * Send receipt mail to the customer
     * @param array $receipt_data
     * @param integer $total_value
     */
    public function sendReceiptMailToCustomer( $receipt_data, $total_value ) {
        $data = [
            'receipt_data'=>$receipt_data, 
            'total_value'=>g::formatCurrency($total_value),
            'customer_mail'=>JWTAuth::parseToken()->authenticate()->email
                ];
        \Mail::queue('email.vouchers.receipt', $data, function($message) use ($data) {
            extract($data);
            $message->to($customer_mail)->subject('Validate Vouchers Receipt');
        });
    }

}
