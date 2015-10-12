<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\EssentialEntities\Transformers;

/**
 * Description of BusinessLogoTransformer
 *
 * @author mohamed
 */
class BusinessLogoTransformer extends Transformer{

    public function beforeStandard( array $item ) {
        $user = (!empty($item['user'])) ? $item['user'] : ["data"=>["user_id"=>(string)$item['user_id']]];
        $business = (!empty($item['business'])) ? $item['business'] : ["data"=>["business_id"=>(string)$item['business_id']]];
        $business_logo_link = config('validateconf.default_business_logos_uri').'/'.$item['name'].'.png';
        $response = [
            "id" => (string)$item['id'],
            "name" => (string)$item['name'],
            "relations"=>[
                "user"=>$user,
                "business" => $business
            ],
            "links" => [
                "business_logo_link" => $business_logo_link
            ]
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }

}
