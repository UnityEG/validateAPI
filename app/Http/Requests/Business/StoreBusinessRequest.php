<?php

namespace App\Http\Requests\Business;

use App\Http\Requests\Request;

class StoreBusinessRequest extends Request
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
            "data.relations.industry.data.industry_id" => ['required', 'integer', 'exists:lu_industries,id'],
//            todo fix business_type_ids validation rules as an array
            "data.relations.business_types.data.business_type_ids" => ['required', 'exists:business_types,id'],
            "data.business_name" => ['required', 'string', 'unique:business,business_name'],
            "data.trading_name" => ['required', 'string', 'unique:business,trading_name'],
            "data.address1" => ['required', 'string'],
            "data.address2" => ['string'],
            "data.phone" => ['string'],
            "data.website" => ['url'],
            "data.business_email" => ['email'],
            "data.contact_name" => ['string'],
            "data.contact_mobile" => ['string'],
            "data.available_hours_mon" => ['string'],
            "data.available_hours_tue" => ['string'],
            "data.available_hours_wed" => ['string'],
            "data.available_hours_thu" => ['string'],
            "data.available_hours_fri" => ['string'],
            "data.available_hours_sat" => ['string'],
            "data.available_hours_sun" => ['string'],
        ];
    }
}
