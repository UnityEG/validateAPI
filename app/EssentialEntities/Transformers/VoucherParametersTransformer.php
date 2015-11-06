<?php

namespace App\EssentialEntities\Transformers;

use GeneralHelperTools;
use App\EssentialEntities\Transformers\Transformer;

class VoucherParametersTransformer extends Transformer {

    function transform( array $item ) {
        return ["data"=>$this->beforeStandard( $item )];
    }
    
    public function beforeStandard( array $item) {
//        todo refactor beforeStandard method
        $response = [
            'id'                    => ( string ) $item[ 'id' ],
            'voucher_type'          => ( string ) $item[ 'voucher_type' ],
            'title'                 => ( string ) $item[ 'title' ],
            'purchase_start'        => ( string ) GeneralHelperTools::formatDateTime($item[ 'purchase_start' ]),
            'purchase_expiry'       => ( string ) GeneralHelperTools::formatDateTime($item[ 'purchase_expiry' ]),
            'is_expire'             => ( boolean ) $item[ 'is_expire' ],
            'is_display'            => ( boolean ) $item[ 'is_display' ],
            'is_purchased'          => ( boolean ) $item[ 'is_purchased' ],
            'valid_from'            => (isset($item['valid_from']))?( string ) GeneralHelperTools::formatDateTime($item[ 'valid_from' ]):'',
            'valid_for_amount'      => (isset($item['valid_for_amount']))?( string ) $item[ 'valid_for_amount' ]:'',
            'valid_for_units'       => (isset($item['valid_for_units']))?( string ) $item[ 'valid_for_units' ]:'',
            'valid_until'           => (isset($item['valid_until']))?( string ) GeneralHelperTools::formatDateTime($item[ 'valid_until' ]):'',
            'is_limited_quantity' => (bool) $item['is_limited_quantity'],
            'quantity'              => (isset($item['quantity'])) ? ( string ) $item[ 'quantity' ] : '',
            'purchased_quantity'    => (isset($item['purchased_quantity']))?( string ) $item[ 'purchased_quantity' ]:'',
            'stock_quantity'        => (isset($item['stock_quantity']))?( string ) $item[ 'stock_quantity' ]:'',
            'short_description'     => (isset($item['short_description'])) ? ( string ) $item[ 'short_description' ] : '',
            'long_description'      => (isset($item['long_description'])) ? ( string ) $item[ 'long_description' ] : '',
            'is_single_use' => (bool)$item['is_single_use'],
            'no_of_uses'            => (isset($item['no_of_uses'])) ? ( string ) $item[ 'no_of_uses' ] : '',
        ];
        $response = array_merge($response, $this->voucherTypeInfo($item));
//        many to one relations
        $response["relations"]["business"] = (!empty($item['business'])) ? $item['business'] : ["data"=>["business_id"=>(string)$item['business_id']]];
        $response["relations"]["user"] = (!empty($item['user'])) ? $item['user'] : ["data"=>["user_id"=>(string)$item['user_id']]];
        $response["relations"]["voucher_image"] = (!empty($item['voucher_image'])) ? $item['voucher_image'] : ["data"=>["voucher_image_id"=>(string)$item['voucher_image_id']]];
//        many to many relations
        (empty($item['use_terms'])) ?  ["data"=>[]]: $response["relations"]["use_terms"] = $item['use_terms'];
        return $response;
    }
    
    /**
     * Return with specific information of each voucher type
     * @param array $item
     * @return array
     */
    private function voucherTypeInfo( array $item) {
        $response = [];
        switch ( $item['voucher_type'] ) {
            case 'gift':
//                todo add empty string if min_value is empty
                $response["min_value"] = (!isset($item['min_value'])) ?  : (string)$item['min_value'];
//                tood add empty string if max_value is empty
                $response["max_value"] = (!isset($item['max_value'])) ?  : (string)$item['max_value'];
                break;
            case 'deal':
//                todo add empty string if retail_value is empty
                $response["retail_value"] = (!isset($item['retail_value'])) ?  : (string)$item['retail_value'];
//                todo add empty string if value is empty
                $response["deal_value"] = (!isset($item['value'])) ?  : (string)$item['value'];
                break;
//            todo continue adding info of the rest of voucher types
        }//switch ( $item['voucher_type'] )
        return $response;
    }
}
