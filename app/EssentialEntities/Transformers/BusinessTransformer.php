<?php

namespace App\EssentialEntities\Transformers;

use App\EssentialEntities\Transformers\Transformer;
/**
 * Description of BusinessTransformer
 *
 * @author mohamed
 */
class BusinessTransformer extends Transformer{
    
    public function transform(array $item) {
        return ["data"=>  $this->beforeStandard($item)];
    }
    
    public function beforeStandard(array $item) {
//        dealing with simple relations (many to one)
        $city = (isset($item['city'])) ? $item['city'] : ["data"=>["city_id"=>(string)$item['city_id']]];
        $region = (isset($item['region'])) ? $item['region'] : ["data"=>["region_id"=>(string)$item['region_id']]];
        $town = (isset($item['town'])) ? $item['town'] : ["data"=>["town_id"=>(string)$item['town_id']]];
        $postcode = (isset($item['postcode'])) ? $item['postcode'] : ["data"=>["postcode_id"=>(string)$item['postcode_id']]];
        $industry = (isset($item['industry'])) ? $item['industry'] : ["data"=>["industry_id"=>(string)$item['industry_id']]];
//        one to many relations ships data
        $item['logo_id'] = (isset($item['logo_id'])) ? $item['logo_id'] : 0;
        $business_logos = (empty($item['business_logos'])) ? ['data'=>["logo_id"=>$item['logo_id']]] : $item['business_logos'];
//        many to many relationships data
        $business_types = (empty($item['business_types'])) ? ["data"=>[]] : $item['business_types'];
        $users = (empty($item['users'])) ? ["data"=>[]] : $item['users'];
        $response = [
            "id" => (string)$item['id'],
            "code" => (string)$item['code'],
            "facebook_page_id" => (isset($item['facebook_page_id'])) ? (string)$item['facebook_page_id'] : '',
            "is_new" => (boolean)$item['is_new'],
            "is_active" => (boolean)$item['is_active'],
            "business_name" => (string)$item['business_name'],
            "trading_name" => (string)$item['trading_name'],
            "bank_account_number" => (empty($item['bank_account_number'])) ? '' : $item['bank_account_number'],
            "address1" => (string)$item['address1'],
            "address2" => (isset($item['address2'])) ? (string)$item['address2'] : '',
            "phone" => (isset($item['phone'])) ? (string)$item['phone'] : '',
            "website" => (isset($item['website'])) ? (string)$item['website'] : '',
            "business_email" => (isset($item['business_email'])) ? (string)$item['business_email'] : '',
            "contact_name" => (isset($item['contact_name'])) ? (string)$item['contact_name'] : '',
            "contact_mobile" => (isset($item['contact_mobile'])) ? (string)$item['contact_mobile'] : '',
            "is_featured" => (boolean)$item['is_featured'],
            "is_display" => (boolean)$item['is_display'],
            "available_hours_mon"=>  (empty($item['available_hours_mon'])) ? '' : (string)$item['available_hours_mon'],
            "available_hours_tue"=>  (empty($item['available_hours_tue'])) ? '' : (string)$item['available_hours_tue'],
            "available_hours_wed"=>  (empty($item['available_hours_wed'])) ? '' : (string)$item['available_hours_wed'],
            "available_hours_thu"=>  (empty($item['available_hours_thu'])) ? '' : (string)$item['available_hours_thu'],
            "available_hours_fri"=>  (empty($item['available_hours_fri'])) ? '' : (string)$item['available_hours_fri'],
            "available_hours_sat"=>  (empty($item['available_hours_sat'])) ? '' : (string)$item['available_hours_sat'],
            "available_hours_sun"=>  (empty($item['available_hours_sun'])) ? '' : (string)$item['available_hours_sun'],
            "relations"=>[
                "city" => $city,
                "region" => $region,
                "town" => $town,
                "postcode" => $postcode,
                "industry" => $industry,
                "business_logos" => $business_logos,
                "business_types" => $business_types,
                "users" => $users
            ]
        ];
        return $response;
    }
}
