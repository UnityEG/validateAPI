<?php

namespace App\Http\Controllers\PurchaseControllers;

use App\EssentialEntities\GeneralHelperTools;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PurchaseControllers\OrdersController;
use App\Http\Controllers\VouchersController;
use App\Http\Models\Voucher;
use App\Http\Requests\PurchaseRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * PurchaseController Responsible for purchasing voucher process
 *
 * @author Mohamed Atef <en.mohamed.atef@gmail.com>
 */
class PurchaseController extends ApiController{
    
    /**
     * Apply Authentication Middleware on all methods
     */
    public function __construct( ) {
        $this->middleware('jwt.auth');
    }
    
    /**
     * Purchase vouchers online
     * @param \App\Http\Requests\PurchaseRequest $request
     */
    public function onlinePurchase(PurchaseRequest $request ) {
//        todo go with the request to the payment gateway and wait for the response
        $order_object = (new OrdersController())->store((double)$request->get('data[tax]', 0, TRUE));
        foreach ( $request->get('data[vouchers]', [], TRUE) as $purchased_voucher) {
            $purchased_voucher['order_id'] = (int)$order_object->id;
            $purchased_voucher_object = $this->createPurchasedVoucher($purchased_voucher);
            $this->sendVirtualVoucherMail($purchased_voucher_object);
            $receipt_data[] =$this->vouchersReceipt($purchased_voucher_object);
            $total_value = (!isset($total_value))? $purchased_voucher_object->value : $total_value + $purchased_voucher_object->value;
        }//foreach ( $request->get('data') as $purchased_voucher)
        $total_value_with_tax = $total_value + (double)$order_object->tax;
        $this->sendReceiptMailToCustomer($receipt_data, $total_value_with_tax);
        return $this->respond('Successful purchasing process and the receipt has been sent to your email address');
    }
    
    /**
     * Purchase vouchers instore
     * @param \App\Http\Requests\PurchaseRequest $request
     */
    public function instorePurchase(PurchaseRequest $request) {
//        todo decisions to make with instorePurchase
    }
    
    /**
     * Create purchased voucher In Database (vouchers table)
     * @param array $purchased_voucher
     * @return Voucher Voucher object
     */
    private function createPurchasedVoucher( array $purchased_voucher ) {
        $purchased_voucher_to_create = [
            'voucher_parameter_id'=>(int)$purchased_voucher['relations']['voucher_parameter']['data']['voucher_parameter_id'],
            'order_id' => $purchased_voucher['order_id'],
            'recipient_email'=>$purchased_voucher['recipient_email'],
            'message'=>$purchased_voucher['message']
        ];
        (!isset($purchased_voucher['value'])) ? : $purchased_voucher_to_create['value'] = $purchased_voucher['value'];
        (!isset($purchased_voucher['delivery_date'])) ?  : $purchased_voucher_to_create['delivery_date'] = $purchased_voucher['delivery_date'];
        return (new VouchersController())->store($purchased_voucher_to_create);
    }
    
    /**
     * Send Virtual Voucher Mail
     * @param Voucher $purchased_voucher_object
     * @param string $MailBodyView Mail template to be sent
     */
    private function sendVirtualVoucherMail(Voucher $purchased_voucher_object, $MailBodyView='email.vouchers.virtualVoucher') {
        // Generate Virtual Voucher
        $data = $this->generateVirtualVoucher($purchased_voucher_object);
        $data['email_to_email'] = (!is_null($data['recipient_email'])) ? $data['recipient_email'] : $data['customer_email'];
        $data['email_to_name'] = (!is_null($data['recipient_email'])) ? $data['recipient_email'] : $data['customer_name'];
        extract($data);
        // set ini_get to 180 seconds to take a suitable uploading attachements with the mail
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
        // For security delete virtual voucher file after use it
        $this->unlinkVirtualVoucher($voucher_filename);
    }
    
    /**
     * Generate Virtual Voucher
     * @param \App\Http\Models\Voucher $purchased_voucher_object
     * @return array
     */
    private function generateVirtualVoucher(Voucher $purchased_voucher_object) {
        // Gathering Data for email
        $data = $this->getDataForEmail($purchased_voucher_object);
        // Generate virtual voucher image 
        $voucher_filename = GeneralHelperTools::voucher($data);
        // Add $voucher_filename to $data array
        $data['voucher_filename'] = $voucher_filename;
        return $data;
    }
    
    /**
     * Get prepared data for email to be sent
     * @param \App\Http\Models\Voucher $purchased_voucher_object
     * @return array
     */
    private function getDataForEmail(Voucher $purchased_voucher_object) {
        $business_logo_object = $purchased_voucher_object->voucherParameter->business->getActiveLogo();
        $business_logo_filename = (is_object($business_logo_object)) ? config( 'validateconf.default_business_logos_path') . $business_logo_object->name . '.png' : 'voucher/images/validate_logo.png';
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
    
    /**
     * Delete Virtual voucher if found for security reason
     * @param string $voucher_filename
     */
    private function unlinkVirtualVoucher($voucher_filename) {
        // For security delete virtual voucher file after use it
        if (file_exists($voucher_filename)) {
            unlink($voucher_filename);
        }//if (file_exists($voucher_filename))
    }
    
    /**
     * Prepare receipt data
     * @param \App\Http\Models\Voucher $purchased_voucher_object
     * @return array
     */
    private function vouchersReceipt(  Voucher $purchased_voucher_object ) {
        return [
            'voucher_title' => $purchased_voucher_object->voucherParameter->title,
            'voucher_value' => GeneralHelperTools::formatCurrency($purchased_voucher_object->value),
            'recipient_email' => $purchased_voucher_object->recipient_email ,
            'delivery_date' => GeneralHelperTools::formatDate($purchased_voucher_object->delivery_date),
            'expiry_date' => GeneralHelperTools::formatDate($purchased_voucher_object->expiry_date)
        ];
        
    }
    
    /**
     * Send receipt mail to the customer
     * @param array $receipt_data
     * @param integer $total_value
     */
    private function sendReceiptMailToCustomer( $receipt_data, $total_value ) {
        $data = [
            'receipt_data'  => $receipt_data,
            'total_value'   => GeneralHelperTools::formatCurrency( $total_value ),
            'customer_mail' => JWTAuth::parseToken()->authenticate()->email
        ];
        \Mail::queue('email.vouchers.receipt', $data, function($message) use ($data) {
            extract($data);
            $message->to($customer_mail)->subject('Validate Vouchers Receipt');
        });
    }

}
