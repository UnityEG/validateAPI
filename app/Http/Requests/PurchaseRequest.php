<?php

namespace App\Http\Requests;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class PurchaseRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     * Only Users belongs to user groups that have 'purchase_voucher' rule are authorized
     * @return bool
     */
    public function authorize() {
        return $this->CurrentUserObject->hasRule('purchase_voucher');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
//        todo add vouchers as a container of vouchers data objects
//        todo values and delivery will be used with gift voucher parameters only
//        todo delivery date must not be after valid_from in voucher parameters
//        todo add rules according to voucher parameter type
//        we use loop as we actually don't know how many items will be purchased
        $rules = [];
        foreach ( $this->request->get( 'data' ) as $key=>$voucher_to_purchase ) {
                $rules['data.'.$key.'.relations.voucher_parameter.data.voucher_parameter_id'] = 'required|integer|exists:voucher_parameters,id';
               $rules['data.'.$key.'.value']                = 'required|numeric|min:1';
                $rules['data.'.$key.'.delivery_date']        = 'date_format:d/m/Y';
                $rules['data.'.$key.'.recipient_email']      = 'email';
                $rules['data.'.$key.'.message']              = 'string';
        }//foreach ( $this->request->get( 'data' ) as $key=>$voucher_to_purchase )
        return $rules;
    }
    
    /**
     * Customize error messages
     * @return array
     */
    public function messages( ) {
        $error_messages = [];
        foreach ( $this->request->get('data') as $key => $voucher_to_purchase ) {
                $error_messages['data.'.$key.'.relations.voucher_parameter.data.voucher_parameter_id.required'] = 'voucher_parameter_id is required';
                $error_messages['data.'.$key.'.relations.voucher_parameter.data.voucher_parameter_id.integer'] = 'voucher_parameter_id must be integer';
                $error_messages['data.'.$key.'.relations.voucher_parameter.data.voucher_parameter_id.exists'] = 'invalid voucher';
                $error_messages['data.'.$key.'.value.required'] = 'value is required';
                $error_messages['data.'.$key.'.value.numeric'] = 'value must be numeric';
                $error_messages['data.'.$key.'.delivery_date.date_format'] = 'format must be d/m/Y H:i';
                $error_messages['data.'.$key.'.recipient_email.email'] = 'recipient_email must be valid email address';
                $error_messages['data.'.$key.'.message.string'] = 'message must be string';
        }//foreach ( $this->request->get('data') as $key => $voucher_to_purchase )
        return $error_messages;
    }

}
