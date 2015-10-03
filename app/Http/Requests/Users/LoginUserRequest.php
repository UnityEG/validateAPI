<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;

class LoginUserRequest extends Request
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
            "data.email"=>"required|email|exists:users,email",
            "data.password" =>"required|string"
        ];
    }
    
    public function messages( ) {
        return [
            'data.email.exists' => 'invalid email or password'
        ];
    }
}
