<?php

namespace app\Http\Requests\Vouchers\VoucherParameters;

use App\Http\Requests\Request;

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
            'data.retail_value'                                  => 'sometimes|required|numeric',
            'data.value'                                         => 'sometimes|required|numeric',
            'data.min_value'                                     => 'sometimes|required|numeric',
            'data.max_value'                                     => 'sometimes|required|numeric',
            'data.is_valid_during_month'                         => 'boolean',
            'data.discount_percentage'                           => 'sometimes|required|numeric'
        ];
        return $common_rules;
    }
    
    public function messages( ) {
        return [
            'user_id.required' => 'user_id is necessary required'
        ];
    }
    
//    todo add check for specific fields according to the type of the voucher method
}
