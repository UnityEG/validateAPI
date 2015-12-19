<?php

namespace App\Http\Requests\Industries;

use App\Http\Requests\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateIndustryRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * Users can just update their accounts.
     * Users have rule user_update can update any user account.
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
            "data.attributes.industry" => ['sometimes', 'required', 'string', 'unique:lu_industries,industry,'.$this->route()->parameter( 'id')],
        ];
    }
}
