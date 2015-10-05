<?php

namespace App\aaa\Transformers;

/**
 * Transform user array to suit standard Json
 *
 * @author Mohamed Atef <en.mohamed.atef at gmail.com>
 */
class UserTransformer extends Transformer {

    public function transform( $item ) {
        return [
            "data"=>  $this->beforeStandard( $item )
        ];
    }

    public function beforeStandard( $item ) {
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
            "created_at"       => ( string ) $item[ 'created_at' ],
            "updated_at"       => ( string ) $item[ 'updated_at' ],
            "relations"        => [
                "city"     => [
                    "data" => [
                        "city_id" => ( string ) $item[ 'city_id' ]
                    ]
                ],
                "region"   => [
                    "data" => [
                        "region_id" => ( string ) $item[ 'region_id' ]
                    ]
                ],
                "town"     => [
                    "data" => [
                        "town_id" => ( string ) $item[ 'town_id' ]
                    ]
                ],
                "postcode" => [
                    "data" => [
                        "postcode_id" => ( string ) $item[ 'postcode_id' ]
                    ]
                ]
            ]
        ];
        if ( isset($item['token']) && !empty($item['token']) ) {
            $response_token['token'] = (string) $item['token'];
            $response = $response_token+$response;
        }
        return $response;
    }

}
