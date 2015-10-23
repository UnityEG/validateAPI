<?php

namespace App\Http\Requests\Vouchers\VoucherParameters;

use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\VoucherParameter;

class UpdateVoucherParametersRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     * Only users belong to groups that have rule "voucher_parameter_update" will be authorized
     * @return bool
     */
    public function authorize() {
        return ($this->CurrentUserObject->hasRule('voucher_parameter_update')) ? TRUE : FALSE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
//        todo refactor rules method
        $this->voucherTypeCheckRule();
        $this->voucherPurchasedCheckRule();
        $common_rules = [
            'data.id'                                            => 'required|integer|min:1|exists:voucher_parameters,id|voucher_type_check|voucher_purchased_check',
            'data.relations.voucher_image.data.voucher_image_id' => 'sometimes|required|exists:voucher_images,id',
            'data.relations.use_terms.data.use_term_ids'         => 'sometimes|required|array',
            'data.attributes.title'                              => 'sometimes|required|string',
            'data.attributes.purchase_start'                     => 'required_with:data.attributes.purchase_expiry|date_format:d/m/Y H:i|before:data.attributes.purchase_expiry|after:today|before:next year',
            'data.attributes.purchase_expiry'                       => 'required_with:data.attributes.purchase_start|date_format:d/m/Y H:i|after:data.attributes.purchase_start|before:next year',
            'data.attributes.is_expire'                          => 'boolean',
            'data.attributes.is_display'                         => 'boolean',
            'data.attributes.valid_from'                         => ['required_with:data.attributes.valid_for_amount,data.attributes.valid_for_units,data.attributes.valid_until', 'date_format:d/m/Y H:i', 'after:data.attributes.purchase_start', 'before:next year'],
            'data.attributes.valid_for_amount'                   => ['required_with:data.attributes.valid_for_units', 'integer', 'min:1'],
            'data.attributes.valid_for_units'                    => 'required_with:data.attributes.valid_for_amount|alpha|size:1|in:h,d,w,m',
            'data.attributes.valid_until'                        => ['sometimes', 'required', 'date_format:d/m/Y H:i', 'after:data.attributes.valid_from', 'before:next year'],
            'data.attributes.quantity'                           => 'sometimes|required|integer|min:1',
            'data.attributes.short_description'                  => 'string',
            'data.attributes.long_description'                   => 'string',
            'data.attributes.no_of_uses'                         => 'integer',
        ];
        if ( $this->request->get('data[attributes][valid_from]', FALSE, TRUE) ) {
            if ( !$this->request->get('data[attributes][valid_until]', FALSE, TRUE) && !$this->request->get('data[attributes][valid_for_amount]', FALSE, TRUE)) {
                $common_rules['data.attributes.valid_until'][0] = '';
            }//if ( !$this->request->get('data[attributes][valid_until]', FALSE, TRUE) && !$this->request->get('data[attributes][valid_for_amount]', FALSE, TRUE))
        }//if ( $this->request->get('data[attributes][valid_from]', FALSE, TRUE) )
        (!$this->request->get( 'data[attributes][valid_until]', '', TRUE)) ? : $common_rules['data.attributes.valid_from'][] = 'before:data.attributes.valid_until';
        if( $valid_for_units = $this->request->get( 'data[attributes][valid_for_units]', FALSE, TRUE) ){
            switch ( $valid_for_units ) {
                case 'h':
                    $common_rules['data.attributes.valid_for_amount'][] = 'max:8760';
                    break;
                case 'd':
                    $common_rules['data.attributes.valid_for_amount'][] = 'max:365';
                    break;
                case 'w':
                    $common_rules['data.attributes.valid_for_amount'][] = 'max:48';
                    break;
                case 'm' :
                    $common_rules['data.attributes.valid_for_amount'][] = 'max:12';
                    break;
            }//switch ( $valid_for_units )
        }//if($valid_for_units = $this->request->get('data[attributes][valid_for_units]', FALSE, TRUE))
        return (array_merge_recursive( $common_rules, $this->voucherSpecificTypeFieldRules(), $this->addUseTremRules() ));
    }

    public function messages() {
        return [
            "voucher_type_check" => "voucher type mismatch",
            "voucher_purchased_check" => "This voucher already purchased so It cannot be updated"
        ];
    }
    
    /**
     * Extend validation rules with voucher_type_check rule to check the voucher before updating
     */
    private function voucherTypeCheckRule( ) {
        Validator::extend('voucher_type_check', function($attribute, $value, $parameters){
            $route_method_name = $this->route()->getName();
            $voucher_type = VoucherParameter::find($value)->voucher_type;
            switch ( $route_method_name) {
                case 'VoucherParameters.updateGiftVoucherParameters':
                    $result = ('gift' === $voucher_type) ? TRUE : FALSE;
                    break;
                case 'VoucherParameters.updateDealVoucherParameters':
                    $result = ('deal' === $voucher_type) ? TRUE : FALSE;
                    break;
//                todo continue add check rule for other voucher types
                default:
                    $result = FALSE;
                    break;
            }//switch ( $route_method_name)
            return $result;
        });
    }
    
    /**
     * Extend validation rules with voucher_purchased_check rule to check if the voaucher has been purchased before updating
     */
    private function voucherPurchasedCheckRule( ) {
        Validator::extend('voucher_purchased_check', function($attribute, $value, $parameters){
            return (!VoucherParameter::find($value)->is_purchased);
        });
    }
    
    /**
     * Add rules for specific types of vouchers
     * @return array array of rules
     */
    private function voucherSpecificTypeFieldRules( ) {
        $voucher_specific_type_rules = [];
        $route_method_name = $this->route()->getName();
        switch ( $route_method_name) {
            case 'VoucherParameters.updateGiftVoucherParameters':
                $voucher_specific_type_rules['data.attributes.min_value'] = ['required_with:data.attributes.max_value', 'numeric', 'between:20,'.$this->request->get('data[attributes][max_value]', 0, TRUE)];
                $voucher_specific_type_rules['data.attributes.max_value'] = ['required_with:data.attributes.min_value', 'numeric', 'min:'.$this->request->get( 'data[attributes][min_value]', 0, TRUE)];
                break;
            case 'VoucherParameters.updateDealVoucherParameters':
                $voucher_specific_type_rules['data.attributes.retail_value'] = ['required_with:data.attributes.value', 'numeric', 'min:'.$this->request->get( 'data[attributes][value]', 0, TRUE)];
                $voucher_specific_type_rules['data.attributes.value'] = ['required_with:data.attributes.retail_value', 'numeric', 'between:20,'.$this->request->get( 'data[attributes][retail_value]', 0, TRUE)];
                break;
        }//switch ( $route_method_name)
//        todo add other rules according to the rest of voucher types
        return $voucher_specific_type_rules;
    }
    
    /**
     * Add rules for user term ids
     * @return array
     */
    private function addUseTremRules( ) {
        $rules = [];
        foreach ( $this->request->get('data[relations][use_terms][data][use_term_ids]', [], TRUE) as $key => $value ) {
            $rules['data.relations.use_terms.data.use_term_ids.'.$key] = ['required', 'integer', 'exists:use_terms,id'];
        }
        return $rules;
    }
}
