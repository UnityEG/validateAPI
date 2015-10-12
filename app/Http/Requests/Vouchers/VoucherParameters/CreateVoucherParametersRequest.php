<?php

namespace app\Http\Requests\Vouchers\VoucherParameters;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Validator;

class CreateVoucherParametersRequest extends Request {

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
        $common_rules = [
            'data.relations.business.data.business_id'           => 'required|integer|exists:business,id',
            'data.relations.user.data.user_id'                   => 'required|integer|exists:users,id',
            'data.relations.voucher_image.data.voucher_image_id' => 'required|exists:voucher_images,id',
//            fix use_term_ids validation rules as an array
            'data.relations.use_terms.data.use_term_ids'         => 'exists:use_terms,id',
            'data.title'                                         => 'required|string',
            'data.purchase_start'                                => 'date_format:d/m/Y H:i',
            'data.purchase_expiry'                               => 'date_format:d/m/Y H:i',
            'data.valid_from'                                    => 'date_format:d/m/Y H:i',
            'data.valid_for_amount'                              => 'sometimes|required|integer',
            'data.valid_for_units'                               => 'sometimes|required|alpha|size:1|in:d,m,y',
            'data.valid_until'                                   => 'date_format:d/m/Y H:i',
            'data.quantity'                                      => ['sometimes', 'required', 'integer', 'min:1' ],
            'data.short_description'                             => 'string',
            'data.long_description'                              => 'string',
            'data.no_of_uses'                                    => 'integer',
        ];
        $final_rules = array_merge($common_rules, $this->voucherSpecificTypeFieldRules());
        return $final_rules;
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
                $voucher_specific_type_rules['data.min_value'] = ['required', 'numeric'];
                $voucher_specific_type_rules['data.max_value'] = ['required', 'numeric'];
                break;
            case 'VoucherParameters.storeDealVoucherPatameters':
                $voucher_specific_type_rules['retail_value'] = ['required', 'numeric'];
                $voucher_specific_type_rules['value'] = ['required', 'numeric'];
                break;
//            todo add the rest rules for the rest of voucher types
        }//switch ( $route_method_name )
        return $voucher_specific_type_rules;
    }
    
//    todo create rule to be sure that valid_for_amount is [356 day, 12 month, 48 week]
}
