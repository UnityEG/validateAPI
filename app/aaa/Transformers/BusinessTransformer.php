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
//                "business_logo" => $item['logo'],
                "city" => $item['city'],
                "region" => $item['region'],
                "town" => $item['town'],
                "postcode" => $item['postcode'],
                "industry" => $item['industry'],
                "business_types" => $item['business_types'],
                "users" => $item['users']
            ]
        ];
        return $response;
    }
}
