<?php

namespace App\aaa\Transformers;

/**
 * Description of TownTransformer
 *
 * @author mohamed
 */
class TownTransformer extends Transformer{
    
    public function transform( array $item) {
        return ["data"=> $this->beforeStandard($item)];
    }


    public function beforeStandard( array $item ) {
        $response = [
            "id" => (string)$item['id'],
            "nz_town" => (string)$item['nz_town']
        ];
        return $response;
    }
}
