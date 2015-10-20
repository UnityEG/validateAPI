<?php

namespace app\Http\Requests\Vouchers\VoucherParameters;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Validator;

class CreateVoucherParametersRequest extends Request {
//todo change class name to be StoreVoucherParametersRequest
    /**
     * Determine if the user is authorized to make this request.
     * Only users belong to groups that have rule "voucher_parameter_store" will be authorized
     * @return bool
     */
    public function authorize() {
        return ($this->CurrentUserObject->hasRule( 'voucher_parameter_store')) ? TRUE : FALSE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $common_rules = [
            'data.relations.business.data.business_id'           => 'required|integer|exists:business,id',
            'data.relations.voucher_image.data.voucher_image_id' => 'required|exists:voucher_images,id',
            'data.relations.use_terms.data.use_term_ids'         => 'required|array',
            'data.title'                                         => 'required|string',
            'data.purchase_start'                                => 'required|date_format:d/m/Y H:i|after:today|before:'.$this->request->get( 'data[purchase_expiry]', '', TRUE),
            'data.purchase_expiry'                               => 'required|date_format:d/m/Y H:i',
            'data.valid_from'                                    => 'date_format:d/m/Y H:i',
            'data.valid_for_amount'                              => 'sometimes|required|integer',
            'data.valid_for_units'                               => 'sometimes|required|alpha|size:1|in:d,m,y',
            'data.valid_until'                                   => 'date_format:d/m/Y H:i',
            'data.quantity'                                      => ['sometimes', 'required', 'integer', 'min:1' ],
            'data.short_description'                             => 'required|string',
            'data.long_description'                              => 'required|string',
            'data.no_of_uses'                                    => 'integer',
        ];
        return array_merge_recursive($common_rules, $this->voucherSpecificTypeFieldRules(), $this->addUseTermRules( $this->request->get( 'data[relations][use_terms][data][use_term_ids]', [], TRUE)));
    }
    
    public function messages( ) {
        return [
            'user_id.required' => 'user_id is necessary required',
            'voucher_specific_type_field_check' => 'all value fields are required'
        ];
    }
    
    /**
     * Add Rules for specific types of vouchers
     * @return array
     */
    private function voucherSpecificTypeFieldRules( ) {
        $voucher_specific_type_rules = [];
        $route_method_name = $this->route()->getName();
        switch ( $route_method_name ) {
            case 'VoucherParameters.storeGiftVoucherParameters':
//                todo add rule that max_value is must be greater than min_value
                $voucher_specific_type_rules['data.min_value'] = ['required', 'numeric', 'min:1', 'max:'.$this->request->get( 'data[max_value]', 0, TRUE)];
                $voucher_specific_type_rules['data.max_value'] = ['required', 'numeric', 'min:'.$this->request->get('data[min_value]', 0, TRUE)];
                break;
            case 'VoucherParameters.storeDealVoucherPatameters':
                $voucher_specific_type_rules['retail_value'] = ['required', 'numeric'];
                $voucher_specific_type_rules['value'] = ['required', 'numeric'];
                break;
//            todo add the rest rules for the rest of voucher types
        }//switch ( $route_method_name )
        return $voucher_specific_type_rules;
    }
    
    /**
     * Add Rules for use term ids
     * @param array $use_terms_array
     * @return array
     */
    private function addUseTermRules( array $use_terms_array) {
        $rules = [];
        foreach ( $use_terms_array as $key => $value ) {
            $rules['data.relations.use_terms.data.use_term_ids.'.$key] = ['required', 'integer', 'exists:use_terms,id'];
        }
        return $rules;
    }
    
//    todo create rule to be sure that valid_for_amount is [356 day, 12 month, 48 week]
}
