<?php

namespace App\Http\Requests\Vouchers\VoucherValidationRequests;

use GeneralHelperTools;
use App\Http\Models\Voucher;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Validator;

class ValidateVoucherRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
//        todo add authorization rules that the business created voucher will be allowed to validate it
        $voucher_parameter_object = Voucher::find($this->request->getInt('data[relations][voucher][data][voucher_id]', 0, true));
        if ( !is_object( $voucher_parameter_object ) ) {
            $this->ForbiddenMessage = "Invalid Voucher Parameter";
            return false;
        }//if ( !is_object( $voucher_parameter_object ) )
        if ( $voucher_parameter_object->business->id != $this->request->getInt( 'data[relations][business][data][business_id]', 0, TRUE) ) {
            $this->ForbiddenMessage = 'Voucher code is not valid for this merchant';
            return false;
        }//if ( $voucher_parameter_object->business->id != $this->request->getInt( 'data[relations][business][data][business_id]', 0, TRUE) )
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
//        Expire validation rule
        $this->expireVoucherValidationRule();
//        Balance validation rule
        $this->maxRedeemValueValidationRule();
        $voucher_validation_rules = [
            "data.value"=> 'required|numeric|min:1|max_redeem_value',
            "data.relations.voucher.data.voucher_id" => ['required', 'integer', 'exists:vouchers,id', 'voucher_expire' ],
            "data.relations.business.data.business_id" => ['required', 'integer', 'exists:business,id']
        ];

        return $voucher_validation_rules;
    }

    /**
     * Customize error messages
     * @return array
     */
    public function messages() {
        return[
            'data.value.required'                        => 'Value is required to validate a voucher',
            'data.value.numeric'                         => 'Value must be a valid number',
            'data.value.min'                             => 'Value must be greater than zero',
            'max_redeem_value'                           => 'not enough balance',
            'voucher_expire'                             => 'This voucher is invalid',
            'data.relations.voucher.voucher_id.required' => 'voucher_id is required',
            'data.relations.voucher.voucher_id.integer'  => 'voucher_id must be integer',
            'data.relations.voucher.voucher_id.exists'   => 'voucher_id does not exist'
        ];
    }

    /**
     * voucher_expire custom validation rule
     */
    private function expireVoucherValidationRule(  ) {
        Validator::extend('voucher_expire', function($attribute, $value, $parameters){
            $voucher_object = Voucher::findOrFail((int)$value);
            return ('valid' === $voucher_object->status) ? TRUE : FALSE;
        });
    }
    
    /**
     * max_redeem_value custom validation rule
     */
    private function maxRedeemValueValidationRule( ) {
        Validator::extend( 'max_redeem_value', function($attribute, $value, $parameters) {
            $voucher_id = (int)GeneralHelperTools::arrayKeySearchRecursively($this->request->get('data'), 'voucher_id');
            $voucher_object = Voucher::findOrFail($voucher_id);
            if ( !is_null( $voucher_object ) ) {
                return ($voucher_object->balance >= (double)$value);
            }//if ( !is_null( $voucher_object ) )
            return FALSE;
        } );
    }

}
