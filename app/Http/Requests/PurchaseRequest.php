<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PurchaseRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'              => 'required|integer|exists:users,id',
            'voucher_parameter_id' => 'required|integer|exists:voucher_parameters,id',
//            'status' =>'required|in:valid,invalid,validated',
//            'code'=>'reqired|integer|size:9',
            'value'                => 'sometimes|required|numeric',
//            'balance'              => 'sometimes|required|numeric',
//            'is_gift'              => 'boolean',
            'delivery_date'        => 'date_format:d/m/Y H:i',
            'recipient_email'      => 'email',
            'message'              => 'string',
        ];
    }
}
