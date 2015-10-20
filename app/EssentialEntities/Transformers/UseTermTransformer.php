<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\EssentialEntities\Transformers;

/**
 * Description of UseTermTransformer
 *
 * @author mohamed
 */
class UseTermTransformer extends Transformer{

    public function beforeStandard( array $item ) {
        $response = [
            "id" => (string)$item['id'],
            "name" => (string)$item['name'],
            "list_order" => (string)$item['list_order']
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }

}
