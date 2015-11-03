<?php

namespace App\EssentialEntities\Transformers;

use App\EssentialEntities\Transformers\Transformer;

class VoucherValidationLogTransformer extends Transformer {

    function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }
    
    public function beforeStandard( array $item) {
//        todo modify beforeStandard method to deal with greedy data
        return [
            "id"        => ( string ) $item[ "id" ],
            "value"     => ( string ) $item[ "value" ],
            "balance"   => ( string ) $item[ "balance" ],
            "log"       => ( string ) $item[ "log" ],
            "relations" => [
                "voucher"  => [
                    "data" => [
                        "voucher_id" => ( string ) $item[ 'voucher_id' ]
                    ]
                ],
                "business" => [
                    "data" => [
                        ["business_id" => ( string ) $item[ "business_id" ] ]
                    ]
                ],
                "user"     => [
                    "data" => [
                        ["user_id" => ( string ) $item[ 'user_id' ] ]
                    ]
                ]
            ]
        ];
    }
}
