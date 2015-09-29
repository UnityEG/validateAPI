<?php

namespace App\Http\Requests\Vouchers\VoucherParameters;

use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\VoucherParameter;

class UpdateVoucherParametersRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
//        todo add authorization rules to restrict voucher parameter update route
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->voucherTypeCheckRule();
        $this->voucherPurchasedCheckRule();
        $common_rules = [
            'data.id'                                            => 'required|integer|min:1|exists:voucher_parameters,id|voucher_type_check|voucher_purchased_check',
            'data.relations.business.data.business_id'           => 'sometimes|required|integer|exists:business,id',
            'data.relations.user.data.user_id'                   => 'sometimes|required|integer|exists:users,id',
            'data.relations.voucher_image.data.voucher_image_id' => 'sometimes|required|exists:voucher_images,id',
            'data.relations.use_terms.data.use_term_ids'         => 'sometimes|required|exists:use_terms,id',
            'data.attributes.title'                              => 'sometimes|required|string',
            'data.attributes.purchase_start'                     => 'sometimes|required|date_format:d/m/Y H:i',
            'data.attributes.purchase_expiry'                       => 'sometimes|required|date_format:d/m/Y H:i',
            'data.attributes.is_expire'                          => 'boolean',
            'data.attributes.is_display'                         => 'boolean',
            'data.attributes.valid_from'                         => 'sometimes|required|date_format:d/m/Y',
            'data.attributes.valid_for_amount'                   => 'sometimes|required|integer',
            'data.attributes.valid_for_units'                    => 'sometimes|required|alpha|size:1|in:d,m,y',
            'data.attributes.valid_until'                        => 'sometimes|required|date_format:d/m/Y',
            'data.attributes.quantity'                           => 'sometimes|required|integer|min:1',
            'data.attributes.short_description'                  => 'string',
            'data.attributes.long_description'                   => 'string',
            'data.attributes.no_of_uses'                         => 'integer',
            'data.attributes.retail_value'                       => 'sometimes|required|numeric',
            'data.attributes.value'                              => 'sometimes|required|numeric',
            'data.attributes.min_value'                          => 'sometimes|required|numeric',
            'data.attributes.max_value'                          => 'sometimes|required|numeric',
            'data.attributes.discount_percentage'                => 'sometimes|required|numeric'
        ];
        return $common_rules;
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
}
