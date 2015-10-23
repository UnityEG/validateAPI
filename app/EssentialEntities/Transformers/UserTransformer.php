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
//            todo remove created_at and updated_at
            "created_at"       => ( string ) $item[ 'created_at' ],
            "updated_at"       => ( string ) $item[ 'updated_at' ],
            "relations"        => [
                "city"     => $city,
                "region"   => $region,
                "town"     => $town,
                "postcode" => $postcode
            ]
        ];
//        complex relations (many to many)
        (empty($item['user_groups'])) ? :$response['relations']["user_groups"] = $item['user_groups'];
        (empty($item['business'])) ? : $response['relations']['business'] = $item['business'];
//        Add token if exist
        (empty($item['token'])) ?  : $response['token'] = (string)$item['token'];
        return $response;
    }

}
