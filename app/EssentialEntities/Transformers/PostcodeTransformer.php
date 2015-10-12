<?php

namespace App\EssentialEntities\Transformers;

/**
 * Description of PostcodeTransformer
 *
 * @author mohamed
 */
class PostcodeTransformer extends Transformer{

    public function beforeStandard( array $item ) {
        $response = [
            "id" => (string)$item['id'],
            "postcode" => (string)$item['postcode']
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }

}
