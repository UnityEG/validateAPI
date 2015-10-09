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
        $response = [
            "id" => (string)$item['id'],
            "name" => (string)$item['name'],
            "relations"=>[
                "user"=>[
                    "data" => [
                        "user_id" => (string)$item['user_id']
                    ]
                ],
                "business" => [
                    "data" => [
                        "business_id" => (string)$item['business_id']
                    ]
                ]
            ]
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }

}
