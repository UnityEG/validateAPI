<?php

namespace App\Http\Requests;

use App\Http\Models\VoucherParameter;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Validator;

class InstorePurchaseRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
//        todo create authorization rules to allow instore purchasing
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->expireVoucherValidationRule();
        $rules["tax"] = ['numeric'];
        foreach ( $this->request->get('data[vouchers]', [], TRUE) as $key => $voucher_to_purchase ) {
            $rules["data.vouchers.{$key}.relations.voucher_parameter.data.voucher_parameter_id"] = ['required', 'integer', 'exists:voucher_parameters,id', 'expire_voucher'];
            $rules = array_merge($rules, $this->voucherTypeSpecificValidationRules($voucher_to_purchase['relations']['voucher_parameter']['data']['voucher_parameter_id'], $key));
        }
        return $rules;
    }
    
    public function messages() {
        return [
            'expire_voucher' => 'Expired Voucher Parameter'
        ];
    }
    
    /**
     * 
     * @param type $voucher_parameter_id
     * @param type $key
     * @return string
     */
    private function voucherTypeSpecificValidationRules($voucher_parameter_id, $key){
        $voucher_parameter_object = VoucherParameter::findOrFail((int)$voucher_parameter_id);
        $rules = [];
        switch ( $voucher_parameter_object->voucher_type ) {
            case 'gift':
                $rules["data.vouchers.{$key}.value"] = ['required', 'numeric', "between:{$voucher_parameter_object->min_value},{$voucher_parameter_object->max_value}"];
                break;
//            todo continue adding the rest voucher specific rules
        }//switch ( $Voucher_parameter_object->voucher_type )
        return $rules;
    }
    
    /**
     * Register custom validation rule (expire_voucher)
     */
    private function expireVoucherValidationRule(){
        Validator::extend('expire_voucher', function($attribute, $value, $parameters){
            $voucher_parameter_object = VoucherParameter::findOrFail((int)$value);
            $now_object = \Carbon\Carbon::now();
            return(!(bool)$voucher_parameter_object->is_expire && $now_object->gt($voucher_parameter_object->purchase_start) && $voucher_parameter_object->purchase_expiry->gt($now_object)) ? TRUE : FALSE;
        });
    }
}
