<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * Users can just update their accounts.
     * Users have rule user_update can update any user account.
     * @return bool
     */
    public function authorize()
    {
        $response = FALSE;
        $current_user_object = JWTAuth::parseToken()->authenticate();
        if ( $current_user_object->isActiveUser() && ($this->request->get('data[id]', 0, TRUE) === (int)$current_user_object->id) ) {
            $response = TRUE;
        }//if ( $current_user_object->isActiveUser() && ($this->request->get('data[id]', 0, TRUE) === (int)$current_user_object->id) )
        if ( $current_user_object->hasRule('user_update') ) {
            $response = TRUE;
        }//if ( $current_user_object->hasRule('user_update') )
        return $response;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "data.id" => ['required', 'integer', 'min:1', 'exists:users,id'],
            "data.relations.city.data.city_id" => ['sometimes', 'required', 'integer', 'min:1', 'exists:lu_nz_cities,id'],
            "data.relations.region.data.region_id" => ['sometimes', 'required', 'integer', 'min:1', 'exists:lu_nz_regions,id'],
            "data.relations.town.data.town_id" => ['sometimes', 'required', 'integer', 'min:1', 'exists:lu_nz_towns,id'],
            "data.relations.postcode.data.postcode_id" => ['sometimes', 'required', 'integer', 'min:1', 'exists:lu_nz_postcodes,id'],
            "data.attributes.is_active" => ['sometimes', 'required', 'boolean'],
            "data.attributes.email" => ['sometimes', 'required', 'email', 'unique:users,email,'.$this->route()->parameter('')],
            "data.attributes.password" => ['sometimes', 'required', 'string', 'min:8', 'regex:/^(?=.*[0-9])(?=.*[a-zA-Z])(?:[a-zA-Z0-9]+)$/'],
            "data.attributes.title" => ['sometimes', 'required', 'max:5'],
            "data.attributes.first_name" => ['sometimes', 'required', 'string'],
            "data.attributes.last_name" => ['sometimes', 'required', 'string'],
            "data.attributes.gender" => ['sometimes', 'required', 'alpha', 'in:male,female'],
            "data.attributes.dob" => ['sometimes', 'required', 'date_format:d/m/Y'],
            "data.attributes.address1" => ['sometimes', 'required', 'string'],
            "data.attributes.address2" => ['sometimes', 'required', 'string'],
            "data.attributes.phone" => ['sometimes', 'required', 'string'],
            "data.attributes.mobile" => ['sometimes', 'required', 'string'],
            "data.attributes.is_notify_deal" => ['sometimes', 'required', 'boolean']
        ];
    }
    
    public function messages( ) {
        return[
            "regex" => "Password must be a minimum 8 characters and include a mix of letters and numbers"
        ];
    }
}
