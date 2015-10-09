<?php

namespace App\EssentialEntities\Transformers;

/**
 * Description of IndustryTransformer
 *
 * @author mohamed
 */
class IndustryTransformer extends Transformer{
    public function beforeStandard( array $item ) {
        $response = [
            "id" => (string)$item['id'],
            "industry" => (string)$item['industry']
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }
}
