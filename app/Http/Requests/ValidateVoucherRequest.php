<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
//Controllers
use App\Http\Controllers\ApiController;
//Models
use App\Http\Models\Voucher;

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
//        Balance validation rule
        \Illuminate\Support\Facades\Validator::extend( 'max_redeem_value', function($attribute, $value, $parameters) {
            $voucher_object = Voucher::find( $parameters[ 0 ] );
            if ( !is_null( $voucher_object ) ) {
                return ($voucher_object->balance >= (double)$value);
            }//if ( !is_null( $voucher_object ) )
            return FALSE;
        } );

        $voucher_validation_rules = [
            "data.value"                        => 'required|numeric|min:1|max_redeem_value:' . $this->request->get( 'data' )[ 'relations' ][ 'voucher' ][ 'voucher_id' ],
            "data.relations.voucher.voucher_id" => ['required', 'integer', 'exists:vouchers,id' ]
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
            'data.relations.voucher.voucher_id.required' => 'voucher_id is required',
            'data.relations.voucher.voucher_id.integer'  => 'voucher_id must be integer',
            'data.relations.voucher.voucher_id.exists'   => 'voucher_id does not exist'
        ];
    }

    /**
     * Customize Json Response
     * @param array $errors
     * @return JsonResponse
     */
    public function response( array $errors ) {
        if ( $this->ajax() || $this->wantsJson() ) {
            return (new ApiController() )->setStatusCode( 417 )->respondWithError( 'invalid parameters', $errors );
        }//if ($this->ajax() || $this->wantsJson())

        return $this->redirector->to( $this->getRedirectUrl() )
                        ->withInput( $this->except( $this->dontFlash ) )
                        ->withErrors( $errors, $this->errorBag );
    }

}
