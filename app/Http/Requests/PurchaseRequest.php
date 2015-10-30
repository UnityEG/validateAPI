<?php

namespace App\Http\Requests;

use App\Http\Models\VoucherParameter;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Validator;

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
        $this->expireVoucherValidationRule();
//        we use loop as we actually don't know how many items will be purchased
        $rules = ["tax" => ['numeric']];
        foreach ( $this->request->get( 'data[vouchers]', [], TRUE ) as $key=>$voucher_to_purchase ) {
                $rules['data.vouchers.'.$key.'.relations.voucher_parameter.data.voucher_parameter_id'] = 'required|integer|exists:voucher_parameters,id|expire_voucher';
                $rules['data.vouchers.'.$key.'.recipient_email']      = 'email';
                $rules['data.vouchers.'.$key.'.message']              = 'string';
                $rules = array_merge($rules, $this->voucherTypeSpecificValidationRules($voucher_to_purchase['relations']['voucher_parameter']['data']['voucher_parameter_id'], $key));
        }//foreach ( $this->request->get( 'data' ) as $key=>$voucher_to_purchase )
        return($rules);
    }
    
    /**
     * Customize error messages
     * @return array
     */
    public function messages( ) {
//        todo modify messages to deal with vouchers array inside data array
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
    
    /**
     * Validation rules for different types of vouchers
     * @param int $id
     * @param int $key
     * @return array
     */
    private function voucherTypeSpecificValidationRules($id, $key){
        $voucher_parameter_object = VoucherParameter::findOrFail((int)$id);
        $rules = [];
        switch ( $voucher_parameter_object->voucher_type ) {
            case 'gift':
                $rules['data.vouchers.'.$key.'.value'] = ['required', 'numeric', 'min:'.$voucher_parameter_object->min_value];
                $rules['data.vouchers.'.$key.'.delivery_date'] = ['date_format:d/m/Y', 'after:today', 'before:'.$voucher_parameter_object->valid_until];
                break;
//            todo continue custom validation rules for specific type of vouchers
        }//switch ( $voucher_parameter_object )
        return $rules;
    }
    
    /**
     * Register custom validation rule (expire_voucher)
     */
    private function expireVoucherValidationRule( ) {
        Validator::extend('expire_voucher', function($attribute, $value, $parameters){
          $voucher_parameter_object = VoucherParameter::findOrFail((int)$value);
          if(!(bool)$voucher_parameter_object->is_expire){
              return TRUE;
          }//if((bool)$voucher_parameter_object->is_expire)
          return FALSE;
        });
    }

}
