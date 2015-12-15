<?php

namespace App\Http\Requests\Business;

use App\Http\Requests\Request;

class UpdateBusinessRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     * Only users belongs to the business OR the groups has the rule "business_update" are authorized
     * @return bool
     */
    public function authorize()
    {
        $response = FALSE;
        (!$this->CurrentUserObject->business()->where('business_id', (int)$this->route()->getParameter('Business'))->exists()) ?  : $response = TRUE;
        (!$this->CurrentUserObject->hasRule( 'business_update')) ?  : $response = TRUE;
        return $response;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $business_id_from_URI = (int)$this->route()->parameter("Business");
        $rules =  [
            "data.id" => ['required', 'integer', 'exists:business,id'],
            "data.relations.business_logo.data.logo_id" => ['sometimes', 'required', 'integer', 'exists:business_logos,id'],
            "data.relations.city.data.city_id" => ['sometimes', 'required', 'integer', 'exists:lu_nz_cities,id'],
            "data.relations.region.data.region_id" => ['sometimes', 'required', 'integer', 'exists:lu_nz_regions,id'],
            "data.relations.town.data.town_id" => ['sometimes', 'required', 'integer', 'exists:lu_nz_towns,id'],
            "data.relations.postcode.data.postcode_id" => ['sometimes', 'required', 'integer', 'exists:lu_nz_postcodes,id'],
            "data.relations.industry.data.industry_id" => ['sometimes', 'required', 'integer', 'exists:lu_industries,id'],
            "data.relations.business_types.data.business_type_ids" => ['sometimes', 'required', 'array' ],
            "data.attributes" => ["required", "array"],
            "data.attributes.is_active" => ['sometimes', 'required', 'boolean'],
            "data.attributes.business_name" => ['sometimes', 'required', 'string', 'unique:business,business_name,'.$business_id_from_URI],
            "data.attributes.trading_name" => ['sometimes', 'required', 'string', 'unique:business,trading_name,'.$business_id_from_URI],
            "data.attributes.bank_account_number" => ['alpha_num', 'size:16'],
            "data.attributes.address1" => ['sometimes', 'required', 'string'],
            "data.attributes.address2" => ['string'],
            "data.attributes.phone" => ['string'],
            "data.attributes.website" => ['string'],
            "data.attributes.business_email" => ['email'],
            "data.attributes.contact_name" => ['string'],
            "data.attributes.contact_mobile" => ['string'],
            "data.attributes.is_featured" => ['sometimes', 'required','boolean'],
            "data.attributes.is_display" => ['sometimes', 'required','boolean']
        ];
        return array_merge($rules, $this->businessTypeIdsValidationRule());
    }
    
    public function messages( ) {
        return [
            "exists" => 'invalid parameters'
        ];
    }
//    custom validation rules
    
    /**
     * Validation Rules for business_type_ids array
     * @return array
     */
    public function businessTypeIdsValidationRule(  ) {
        $business_type_ids = $this->request->get("data")['relations']['business_types']['data']['business_type_ids'];
        if ( !$business_type_ids) {
            return [];
        }//if ( !$business_type_ids)
        $continue_rules = [];
        foreach ( $business_type_ids as $key => $value ) {
            $continue_rules["data.relations.business_types.data.business_type_ids.".$key] = ['exists:business_types,id'];
        }
        return $continue_rules;
    }
}
