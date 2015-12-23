<?php

namespace App\Http\Requests\Vouchers\UseTermRequests;

use App\Http\Requests\Request;

class StoreUseTermRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "data.name" => ['required', 'string', 'unique:use_terms,name'],
            "data.list_order" => ['required', 'integer']
        ];
    }
}
