<?php

namespace App\aaa\Transformers;

use App\aaa\Transformers\Transformer;
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
        $response = [
            "id" => (string)$item['id'],
            "facebook_page_id" => (isset($item['facebook_page_id'])) ? (string)$item['facebook_page_id'] : '',
            "is_active" => (boolean)$item['is_active'],
            "business_name" => (string)$item['business_name'],
            "trading_name" => (string)$item['trading_name'],
            "address1" => (string)$item['address1'],
            "address2" => (isset($item['address2'])) ? (string)$item['address2'] : '',
            "phone" => (isset($item['phone'])) ? (string)$item['phone'] : '',
            "website" => (isset($item['website'])) ? (string)$item['website'] : '',
            "business_email" => (isset($item['business_email'])) ? (string)$item['business_email'] : '',
            "contact_name" => (isset($item['contact_name'])) ? (string)$item['contact_name'] : '',
            "contact_mobile" => (isset($item['contact_mobile'])) ? (string)$item['contact_mobile'] : '',
            "is_featured" => (boolean)$item['is_featured'],
            "is_display" => (boolean)$item['is_display'],
            "created_at" => (string)$item['created_at'],
            "updated_at" => (string)$item['updated_at'],
            "relations"=>[
//                todo repair how to show logo info here "business_logo" => $item['logo'],
                "city" => $city,
                "region" => $region,
                "town" => $town,
                "postcode" => $postcode,
                "industry" => $industry,
            ]
        ];
//        complex relations (many to many)
        (!isset($item['business_types'])) ?  : $response["relations"]["business_types"]=$item['business_types'];
        (!isset($item['users'])) ?  : $response["relations"]["users"] = $item['users'];
        return $response;
    }
}
