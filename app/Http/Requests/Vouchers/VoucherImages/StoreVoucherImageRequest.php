<?php

namespace App\Http\Requests\Vouchers\VoucherImages;

use App\Http\Requests\Request;

class StoreVoucherImageRequest extends Request
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
       return[
           'voucher_image' => 'required|image|max:2096'
       ];
    }
}
