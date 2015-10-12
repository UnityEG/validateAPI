<?php

namespace App\EssentialEntities\Transformers;

/**
 * Description of CityTransformer
 *
 * @author mohamed
 */
class CityTransformer extends Transformer{
    
    public function transform( array $item) {
        return ["data"=>  $this->beforeStandard($item)];
    }
    
    public function beforeStandard( array $item) {
        $response = [
            "id" => (string)$item['id'],
            "nz_city" => (string)$item['nz_city']
        ];
        return $response;
    }
}
