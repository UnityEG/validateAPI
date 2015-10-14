<?php

namespace App\EssentialEntities\Transformers;

/**
 * Description of VoucherImageTransformer
 *
 * @author mohamed
 */
class VoucherImageTransformer extends Transformer{
    
    public function beforeStandard(array $item) {
        return[
            "id" => (string)$item['id'],
            "name" => (string)$item['name'],
            "type" => (string)$item['type'],
            "created_at" => (string)$item['created_at'],
            "updated_at" => (string)$item['updated_at']
        ];
    }
    
    public function transform(array $item) {
        return [
            "data" => $this->beforeStandard( $item )
        ];
    }
}
