<?php

namespace App\aaa\Transformers;

use App\aaa\Transformers\Transformer;

class VoucherParametersTransformer extends Transformer {

    function transform( $item ) {
        return [
            'id'                    => ( string ) $item[ 'id' ],
            'business_id'           => ( string ) $item[ 'business_id' ],
            'user_id'               => ( string ) $item[ 'user_id' ],
            'voucher_image_id'      => ( string ) $item[ 'voucher_image_id' ],
            'voucher_type'          => ( string ) $item[ 'voucher_type' ],
            'title'                 => ( string ) $item[ 'title' ],
            'purchase_start'        => ( string ) $item[ 'purchase_start' ],
            'purchase_expiry'       => ( string ) $item[ 'purchase_expiry' ],
            'is_expire'             => ( boolean ) $item[ 'is_expire' ],
            'is_display'            => ( boolean ) $item[ 'is_display' ],
            'is_purchased'          => ( boolean ) $item[ 'is_purchased' ],
            'valid_from'            => ( string ) $item[ 'valid_from' ],
            'valid_for_amount'      => ( string ) $item[ 'valid_for_amount' ],
            'valid_for_units'       => ( string ) $item[ 'valid_for_units' ],
            'valid_until'           => ( string ) $item[ 'valid_until' ],
            'quantity'              => ( string ) $item[ 'quantity' ],
            'purchased_quantity'    => ( string ) $item[ 'purchased_quantity' ],
            'stock_quantity'        => ( string ) $item[ 'stock_quantity' ],
            'short_description'     => ( string ) $item[ 'short_description' ],
            'long_description'      => ( string ) $item[ 'long_description' ],
            'no_of_uses'            => ( string ) $item[ 'no_of_uses' ],
            'retail_value'          => ( string ) $item[ 'retail_value' ],
            'value'                 => ( string ) $item[ 'value' ],
            'min_value'             => ( string ) $item[ 'min_value' ],
            'max_value'             => ( string ) $item[ 'max_value' ],
            'is_valid_during_month' => ( boolean ) $item[ 'is_valid_during_month' ],
            'discount_percentage'   => ( string ) $item[ 'discount_percentage' ],
            'created_at'            => ( string ) $item[ 'created_at' ],
            'updated_at'            => ( string ) $item[ 'updated_at' ],
        ];
    }

}
