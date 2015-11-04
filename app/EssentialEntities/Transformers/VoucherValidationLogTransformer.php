<?php

namespace App\EssentialEntities\Transformers;

use App\EssentialEntities\Transformers\Transformer;

class VoucherValidationLogTransformer extends Transformer {

    function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }
    
    public function beforeStandard( array $item) {
        $voucher = (empty($item['voucher'])) ? ["voucher"=>["data"=>["voucher_id"=>(string)$item['voucher_id']]]] : $item['voucher'];
        $user = (empty($item['user'])) ? ["user"=>["data"=>["user_id"=>(string)$item['user_id']]]] : $item['user'];
        $business = (empty($item['business'])) ? ["business"=>["data"=>["business_id"=>(string)$item['business_id']]]] : $item['business'];
        return [
            "id"        => ( string ) $item[ "id" ],
            "value"     => ( string ) $item[ "value" ],
            "balance"   => ( string ) $item[ "balance" ],
            "log"       => ( string ) $item[ "log" ],
            "relations" => [
                "voucher"  => $voucher,
                "business" => $business,
                "user"     => $user
            ]
        ];
    }
}
