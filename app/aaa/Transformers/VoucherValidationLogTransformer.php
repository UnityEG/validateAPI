<?php

namespace App\aaa\Transformers;

use App\aaa\Transformers\Transformer;

class VoucherValidationLogTransformer extends Transformer {

    function transform( $item ) {
        return [
                "id" => (string) $item["id"],
                "value" => (string)$item["value"],
                "balance" => (string)$item["balance"],
                "log" => (string)$item["log"],
                "relations" => [
                    "voucher"=>[
                        "data"=>[
                            "voucher_id" => (string)$item['voucher_id']
                        ]
                    ],
                    "business"=>[
                        "data"=>[
                            ["business_id" => (string)$item["business_id"]]
                        ]
                    ],
                    "user"=>[
                        "data"=>[
                            ["user_id"=>(string)$item['user_id']]
                        ]
                    ]
    
                ]
            ];
    }

}
