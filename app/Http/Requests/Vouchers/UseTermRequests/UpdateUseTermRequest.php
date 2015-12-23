<?php

namespace App\Http\Requests\Vouchers\UseTermRequests;

use App\Http\Requests\Request;

class UpdateUseTermRequest extends Request
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
            "data.id" => ['required', 'exists:use_terms,id'],
            "data.attributes.name" => ['sometimes', 'required', 'string', 'unique:use_terms,name,'.(int)$this->route()->parameter( 'id')],
            "data.attributes.list_order" => ['sometimes', 'required', 'integer']
        ];
    }
}
