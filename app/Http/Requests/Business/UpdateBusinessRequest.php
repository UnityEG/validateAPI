<?php

namespace App\Http\Requests\Business;

use App\Http\Requests\Request;

class UpdateBusinessRequest extends Request
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
        $rules =  [
            "data.id" => ['required', 'integer', 'exists:business,id'],
            "data.relations.business_logo.data.logo_id" => ['sometimes', 'required', 'integer', 'exists:business_logos,id'],
            "data.relations.city.data.city_id" => ['sometimes', 'required', 'integer', 'exists:lu_nz_cities,id'],
            "data.relations.region.data.region_id" => ['sometimes', 'required', 'integer', 'exists:lu_nz_regions,id'],
            "data.relations.town.data.town_id" => ['sometimes', 'required', 'integer', 'exists:lu_nz_towns,id'],
            "data.relations.postcode.data.postcode_id" => ['sometimes', 'required', 'integer', 'exists:lu_nz_postcodes,id'],
            "data.relations.industry.data.industry_id" => ['sometimes', 'required', 'integer', 'exists:lu_industries,id'],
            "data.relations.business_types.data.business_type_ids" => ['sometimes', 'required', 'array' ],
            "data.attributes.is_active" => ['sometimes', 'required', 'boolean'],
            "data.attributes.business_name" => ['sometimes', 'required', 'string', 'unique:business,business_name'],
            "data.attributes.trading_name" => ['sometimes', 'required', 'string', 'unique:business,trading_name'],
            "data.attributes.address1" => ['sometimes', 'required', 'string'],
            "data.attributes.address2" => ['string'],
            "data.attributes.phone" => ['string'],
            "data.attributes.website" => ['string'],
            "data.attributes.business_email" => ['email'],
            "data.attributes.contact_name" => ['string'],
            "data.contact_mobile" => ['string'],
            "data.attributes.is_featured" => ['sometimes', 'required','boolean'],
            "data.attributes.is_display" => ['sometimes', 'required','boolean']
        ];
        $final_rules = array_merge($rules, $this->businessTypeIdsValidationRule());
        return $final_rules;
    }
    
    public function messages( ) {
        return [
            "exists" => 'invalid parameters'
        ];
    }
//    custom validation rules
    public function businessTypeIdsValidationRule(  ) {
        
        if ( !isset($this->request->get("data")['relations']['business_types']['data']['business_type_ids'] )) {
            return [];
        }
        $business_type_ids = $this->request->get("data")['relations']['business_types']['data']['business_type_ids'];
        $continue_rules = [];
        foreach ( $business_type_ids as $key => $value ) {
            $continue_rules["data.relations.business_types.data.business_type_ids.".$key] = ['exists:business_types,id'];
        }
        return $continue_rules;
    }
}
