<?php

namespace App\Http\Requests\Vouchers\VoucherParameters;

use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

class UpdateVoucherParametersRequest extends Request
{
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
//        todo continue refine update voucher rule with relocating properties
//        todo extend new validation rule for voucher_type_check
        dd($this->route()->getName());
        $common_rules = [
            'data.id'=>'required|integer|min:1|exists:voucher_parameters,id',
            'business_id'           => 'sometimes|required|integer|exists:business,id',
            'user_id'               => 'sometimes|required|integer|exists:users,id',
            'voucher_image_id'      => 'sometimes|required|exists:voucher_images,id',
            'use_terms'             => 'sometimes|required|exists:use_terms,id',
//            'voucher_type'          => 'required|string|in:gift,deal,birthday,discount,concession',
            'title'                 => 'sometimes|required|string',
            'purchase_start'        => 'date_format:d/m/Y H:i',
            'purchase_end'          => 'date_format:d/m/Y H:i',
            'is_expire'             => 'boolean',
            'is_display'            => 'boolean',
            'valid_from'            => 'date_format:d/m/Y',
            'valid_for_amount'      => 'sometimes|required|integer',
            'valid_for_units'       => 'sometimes|required|alpha|size:1|in:d,m,y',
            'valid_until'           => 'date_format:d/m/Y',
            'quantity'              => 'sometimes|required|integer|min:1',
//            'stock_quantity'        => 'integer',
            'short_description'     => 'string',
            'long_description'      => 'string',
            'no_of_uses'            => 'integer',
            'retail_value'          => 'sometimes|required|numeric',
            'value'                 => 'sometimes|required|numeric',
            'min_value'             => 'sometimes|required|numeric',
            'max_value'             => 'sometimes|required|numeric',
//            'is_valid_during_month' => 'boolean',
            'discount_percentage'   => 'sometimes|required|numeric'
        ];
        return $common_rules;
    }
    
    public function messages( ) {
        return [
            'user_id.required' => 'user_id is necessary required'
        ];
    }
}
