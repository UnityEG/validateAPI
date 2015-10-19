<?php

namespace App\EssentialEntities\Transformers;

/**
 * Description of VoucherImageTransformer
 *
 * @author mohamed
 */
class VoucherImageTransformer extends Transformer{
    
    public function beforeStandard(array $item) {
        $voucher_image_link = config('validateconf.default_voucher_images_uri').'/'.$item['name'].'.png';
        return[
            "id" => (string)$item['id'],
            "name" => (string)$item['name'],
            "voucher_type" => (string)$item['voucher_type'],
            "created_at" => (string)$item['created_at'],
            "updated_at" => (string)$item['updated_at'],
            "links"=>[
                "voucher_image_link" => $voucher_image_link
            ]
        ];
    }
    
    public function transform(array $item) {
        return [
            "data" => $this->beforeStandard( $item )
        ];
    }
}
