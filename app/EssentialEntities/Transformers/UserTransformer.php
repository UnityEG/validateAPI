<?php

namespace App\EssentialEntities\Transformers;

/**
 * Transform user array to suit standard Json
 *
 * @author Mohamed Atef <en.mohamed.atef at gmail.com>
 */
class UserTransformer extends Transformer {

    public function transform( array $item ) {
        return [
            "data"=>  $this->beforeStandard( $item )
        ];
    }

    public function beforeStandard(array $item ) {
//        prepare for greedy data (many to one)
        $city = (isset($item['city'])) ? $item['city'] : ["data"=>["city_id"=>(string)$item['city_id']]];
        $region = (isset($item['region'])) ? $item['region'] : ["data"=>["region_id"=>(string)$item['region_id']]];
        $town = (isset($item['town'])) ? $item['town'] : ["data"=>["town_id"=>(string)$item['town_id']]];
        $postcode = (isset($item['postcode'])) ? $item['postcode'] : ["data"=>["postcode_id"=>(string)$item['postcode_id']]];
//        many to may relationship
        $user_groups = (empty($item['user_groups'])) ? ["data"=>[]] : $item['user_groups'];
        $business = (empty($item['business'])) ? ["data"=>[]] : $item['business'];
        $response = [
            "id"               => ( string ) $item[ 'id' ],
            "facebook_user_id" => (isset($item['facebook_user_id'])) ? ( string ) $item[ 'facebook_user_id' ] : '',
            "is_active"        => ( boolean ) $item[ 'is_active' ],
            "email"            => ( string ) $item[ 'email' ],
            "title"            => ( string ) $item[ 'title' ],
            "first_name"       => ( string ) $item[ 'first_name' ],
            "last_name"        => ( string ) $item[ 'last_name' ],
            "gender"           => ( string ) $item[ 'gender' ],
            "dob"              => ( string ) $item[ 'dob' ],
            "address1"         => ( string ) $item[ 'address1' ],
            "address2"         => ( string ) $item[ 'address2' ],
            "phone"            => ( string ) $item[ 'phone' ],
            "mobile"           => ( string ) $item[ 'mobile' ],
            "is_notify_deal"   => ( boolean ) $item[ 'is_notify_deal' ],
            "relations"        => [
                "city"     => $city,
                "region"   => $region,
                "town"     => $town,
                "postcode" => $postcode,
                "user_groups" => $user_groups,
                "business" => $business,
            ]
        ];
        return $response;
    }

}
