<?php
namespace App\EssentialEntities\Transformers;

/**
 * Description of BusinessTypesTransformer
 *
 * @author mohamed
 */
class BusinessTypesTransformer extends Transformer{

    public function beforeStandard( array $item ) {
        $response = [
            "id" => (string)$item['id'],
            "type" => (string)$item['type']
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }

}
