<?php

namespace App\Http\Requests\Industries;

use App\Http\Requests\Request;

class StoreIndustryRequest extends Request
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
            "data.industry" => ['required', 'string', 'unique:lu_industries,industry'],
        ];
    }
}
