<?php

namespace App\Http\Requests\Business;

use App\Http\Requests\Request;

class StoreBusinessLogoRequest extends Request
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
//            todo how to send business_id with file to store them
            "business_logo" => ['required', 'image', 'max:2096']
        ];
    }
}
