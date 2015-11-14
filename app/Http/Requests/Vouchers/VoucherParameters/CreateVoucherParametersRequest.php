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
//        todo Refactor rules method
        $common_rules = [
            'data.relations.business.data.business_id'           => 'required|integer|exists:business,id',
            'data.relations.voucher_image.data.voucher_image_id' => 'required|exists:voucher_images,id',
            'data.relations.use_terms.data.use_term_ids'         => 'required|array',
            'data.title'                                         => 'required|string',
            'data.purchase_start'                                => 'required|date_format:d/m/Y H:i|after:today|before:data.purchase_expiry|before:next year',
            'data.purchase_expiry'                               => 'required|date_format:d/m/Y H:i|after:data.purchase_start|before:next year',
            'data.valid_from'                                    => ['required', 'date_format:d/m/Y H:i', 'after:data.purchase_start', 'before:next year'],
            'data.valid_for_amount'                              => ['required_with:data.valid_for_units', 'integer', 'min:1'],
            'data.valid_for_units'                               => ['required_with:data.valid_for_amount', 'alpha', 'size:1', 'in:h,d,w,m'],
            'data.valid_until'                                   => 'required_without_all:data.valid_for_amount,data.valid_for_units|date_format:d/m/Y H:i|after:data.valid_from|before:next year',
            'data.quantity'                                      => ['integer', 'min:0' ],
            'data.short_description'                             => 'required|string',
            'data.long_description'                              => 'required|string',
//            todo modify no_of_uses rule to accept 0
            'data.no_of_uses'                                    => 'integer|min:0',
        ];
        (!$this->request->get('data[valid_until]', FALSE, TRUE))? : $common_rules['data.valid_from'][] = 'before:data.valid_until';
        if ( $valid_for_units = $this->request->get('data[valid_for_units]', FALSE, TRUE) ) {
            switch ( $valid_for_units ) {
                case 'h':
                    $common_rules['data.valid_for_amount'][] = 'max:8760';
                    break;
                case 'd':
                    $common_rules['data.valid_for_amount'][] = 'max:365';
                    break;
                case 'w':
                    $common_rules['data.valid_for_amount'][] = 'max:48';
                    break;
                case 'm':
                    $common_rules['data.valid_for_amount'][] = 'max:12';
                    break;
            }//switch ( $valid_for_units )
            
        }//if ( $this->request->get('data[valid_for_units]', FALSE, TRUE) )
        return( array_merge_recursive($common_rules, $this->voucherSpecificTypeFieldRules(), $this->addUseTermRules( $this->request->get( 'data[relations][use_terms][data][use_term_ids]', [], TRUE))));
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
                $voucher_specific_type_rules['data.min_value'] = ['required', 'numeric', 'between:20,'.$this->request->get('data[max_value]', 0, TRUE)];
                $voucher_specific_type_rules['data.max_value'] = ['required', 'numeric', 'min:'.$this->request->get('data[min_value]', 0, TRUE)];
                break;
            case 'VoucherParameters.storeDealVoucherParameters':
                $voucher_specific_type_rules['data.retail_value'] = ['required', 'numeric', 'min:'.$this->request->get('data[value]', 0, TRUE)];
                $voucher_specific_type_rules['data.value'] = ['required', 'numeric', 'between:20,'.$this->request->get('data[retail_value]', 0, TRUE)];
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
}
