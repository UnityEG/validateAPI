<?php

namespace App\aaa\Transformers;

/**
 * Description of RegionTransformer
 *
 * @author mohamed
 */
class RegionTransformer extends Transformer{
    
    public function transform( array $item) {
        return ["data" => $this->beforeStandard($item)];
    }
    
    public function beforeStandard( array $item) {
        $response = [
            "id" => (string)$item['id'],
            "region" => (string)$item['region']
        ];
        return $response;
    }
}
