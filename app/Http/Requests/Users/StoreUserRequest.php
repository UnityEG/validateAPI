<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;

class StoreUserRequest extends Request
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
            "data.relations.city.data.city_id" => ['required', 'integer', 'exists:lu_nz_cities,id'],
            "data.relations.region.data.region_id" => ['required', 'integer', 'exists:lu_nz_regions,id'],
            "data.relations.town.data.town_id" => ['required', 'integer', 'exists:lu_nz_towns,id'],
            "data.relations.postcode.data.postcode_id" => ['required', 'integer', 'exists:lu_nz_postcodes,id'],
            "data.is_active" => ['boolean'],
            "data.email" => ['required', 'email', 'unique:users,email'],
            "data.password" => ['required', 'min:8', 'regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/'],
            "data.title" => ['string', 'max:5'],
            "data.first_name" => ['required', 'string'],
            "data.last_name" => ['required', 'string'],
            "data.gender" => ['alpha', 'in:male,female'],
            "data.dob" => ['required', 'date_format:d/m/Y'],
            "data.address1" => ['required', 'string'],
            "data.address2" => ['sometimes', 'required', 'string'],
            "data.phone" => ['string'],
            "data.mobile" => ['string'],
            "data.is_notify_deal" => ['boolean'],
        ];
    }
    
    public function messages( ) {
        return[
            "regex" => "Password must be a minimum of 8 characters and include a mix of letters and numbers"
        ];
    }
}
