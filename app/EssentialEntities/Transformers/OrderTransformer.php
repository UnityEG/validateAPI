<?php

namespace App\EssentialEntities\Transformers;

/**
 * Description of OrderTransformer
 *
 * @author Mohamed Atef
 */
class OrderTransformer extends Transformer{
    public function beforeStandard( array $item ) {
        $user = (empty($item['user'])) ? ["data"=>["user_id"=>(string)$item['user_id']]] : $item['user'];
        $vouchers = (empty($item['vouchers'])) ? ["data"=>[]] : $item['vouchers'];
        $response = [
            "number" => (string)$item['number'],
            "tax" => (double)$item['tax'],
            "relations" => [
                "user" => $user,
                "vouchers" => $vouchers
            ]
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }
}
