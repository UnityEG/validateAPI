<?php

namespace App\Http\Requests\Vouchers\VoucherValidationRequests;

use App\Http\Models\Voucher;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Validator;

class CheckVoucherRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
//        todo add authorization rules to check vouchers
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->validVoucherCheckRule();
        return [
            'data.voucher_code' => ['required', 'regex:/^\d{9}$/', 'exists:vouchers,code', 'valid_voucher_check']
        ];
    }
    
    /**
     * Get custom error messages
     * @return array
     */
    public function messages() {
        return[
            'data.voucher_code.valid_voucher_check' => 'Invalid Voucher'
        ];
    }
    
    /**
     * Register valid_voucher_check custom validation rule
     */
    private function validVoucherCheckRule() {
        Validator::extend( 'valid_voucher_check', function($attribute, $value, $parameters) {
            $voucher_object = Voucher::where( 'code', $value )->first( ['status' ] );
            if ( is_object( $voucher_object ) ) {
                return ('valid' === $voucher_object->status) ? TRUE : FALSE;
            }//if ( is_object( $voucher_object ) )
            return FALSE;
        } );
    }

}
