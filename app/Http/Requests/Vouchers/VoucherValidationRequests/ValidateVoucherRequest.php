<?php

namespace App\Http\Requests\Vouchers\VoucherValidationRequests;

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
        return true;
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
            "data.value"=> 'required|numeric|min:1|max_redeem_value:' . $this->request->get( 'data' )[ 'relations' ][ 'voucher' ]['data'][ 'voucher_id' ].'|voucher_expire:' . $this->request->get( 'data' )[ 'relations' ][ 'voucher' ]['data'][ 'voucher_id' ],
            "data.relations.voucher.data.voucher_id" => ['required', 'integer', 'exists:vouchers,id' ],
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
            'voucher_expire' => 'This voucher is invalid',
            'data.relations.voucher.voucher_id.required' => 'voucher_id is required',
            'data.relations.voucher.voucher_id.integer'  => 'voucher_id must be integer',
            'data.relations.voucher.voucher_id.exists'   => 'voucher_id does not exist'
        ];
    }

    
    
//    Helper methods
    
    /**
     * voucher_expire custom validation rule
     */
    private function expireVoucherValidationRule(  ) {
        Validator::extend('voucher_expire', function($attribute, $value, $parameters){
            $voucher_object = Voucher::find($parameters[0]);
            return ('valid' === $voucher_object->status) ? TRUE : FALSE;
        });
    }
    
    /**
     * max_redeem_value custom validation rule
     */
    private function maxRedeemValueValidationRule( ) {
        Validator::extend( 'max_redeem_value', function($attribute, $value, $parameters) {
            $voucher_object = Voucher::find( $parameters[ 0 ] );
            if ( !is_null( $voucher_object ) ) {
                return ($voucher_object->balance >= (double)$value);
            }//if ( !is_null( $voucher_object ) )
            return FALSE;
        } );
    }

}
