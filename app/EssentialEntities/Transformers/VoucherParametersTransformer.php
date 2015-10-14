<?php

namespace App\EssentialEntities\Transformers;

use App\EssentialEntities\Transformers\Transformer;

class VoucherParametersTransformer extends Transformer {

    function transform( array $item ) {
        return ["data"=>$this->beforeStandard( $item )];
    }
    
    public function beforeStandard( array $item ) {
        return [
            'id'                    => ( string ) $item[ 'id' ],
            'voucher_type'          => ( string ) $item[ 'voucher_type' ],
            'title'                 => ( string ) $item[ 'title' ],
            'purchase_start'        => ( string ) $item[ 'purchase_start' ],
            'purchase_expiry'       => ( string ) $item[ 'purchase_expiry' ],
            'is_expire'             => ( boolean ) $item[ 'is_expire' ],
            'is_display'            => ( boolean ) $item[ 'is_display' ],
            'is_purchased'          => ( boolean ) $item[ 'is_purchased' ],
            'valid_from'            => (isset($item['valid_from']))?( string ) $item[ 'valid_from' ]:'',
            'valid_for_amount'      => (isset($item['valid_for_amount']))?( string ) $item[ 'valid_for_amount' ]:'',
            'valid_for_units'       => (isset($item['valid_for_units']))?( string ) $item[ 'valid_for_units' ]:'',
            'valid_until'           => (isset($item['valid_until']))?( string ) $item[ 'valid_until' ]:'',
            'quantity'              => (isset($item['quantity'])) ? ( string ) $item[ 'quantity' ] : '',
            'purchased_quantity'    => (isset($item['purchased_quantity']))?( string ) $item[ 'purchased_quantity' ]:'',
            'stock_quantity'        => (isset($item['stock_quantity']))?( string ) $item[ 'stock_quantity' ]:'',
            'short_description'     => (isset($item['short_description'])) ? ( string ) $item[ 'short_description' ] : '',
            'long_description'      => (isset($item['long_description'])) ? ( string ) $item[ 'long_description' ] : '',
            'no_of_uses'            => (isset($item['no_of_uses'])) ? ( string ) $item[ 'no_of_uses' ] : '',
            'retail_value'          => (isset($item['retail_value']))?( string ) $item[ 'retail_value' ]:'',
            'value'                 => (isset($item['value']))?( string ) $item[ 'value' ]:'',
            'min_value'             => (isset($item['min_value']))?( string ) $item[ 'min_value' ]:'',
            'max_value'             => (isset($item['max_value']))?( string ) $item[ 'max_value' ]:'',
            'is_valid_during_month' => (isset($item['is_valid_during_month']))?( boolean ) $item[ 'is_valid_during_month' ]:'',
            'discount_percentage'   => (isset($item['discount_percentage']))?( string ) $item[ 'discount_percentage' ]:'',
            'created_at'            => ( string ) $item[ 'created_at' ],
            'updated_at'            => ( string ) $item[ 'updated_at' ],
            "relations"             => [
                "user"          => [
                    "data" => [
                        'user_id' => ( string ) $item[ 'user_id' ],
                    ]
                ],
                "business"      => [
                    "data" => [
                        'business_id' => ( string ) $item[ 'business_id' ],
                    ]
                ],
                "voucher_image" => [
                    "data" => [
                        'voucher_image_id' => ( string ) $item[ 'voucher_image_id' ],
                    ]
                ],
            ]
        ];
    }

}
