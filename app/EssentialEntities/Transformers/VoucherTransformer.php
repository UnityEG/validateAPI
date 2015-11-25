<?php

namespace App\EssentialEntities\Transformers;

use GeneralHelperTools;
/**
 * Description of VoucherTransformer
 *
 * @author mohamed
 */
class VoucherTransformer extends Transformer{
    public function beforeStandard( array $item ) {
        $voucher_parameter = (empty($item['voucher_parameter'])) ? ["data"=>["voucher_parameter_id"=>(string)$item['voucher_parameter_id']]] : $item['voucher_parameter'];
        $order = (empty($item['order'])) ? ["data"=>["order_id"=>(string)$item['order_id']]] : $item['order'];
        $user = (empty($item['user'])) ? ["data"=>["user_id"=>(string)$item['user_id']]] : $item['user'];
        $voucher_validation_logs = (empty($item['voucher_validation_logs'])) ? ["data"=>[]] : $item['voucher_validation_logs'];
        $response                = [
            'id'                   => ( string ) $item[ 'id' ],
            'status'               => $item[ 'status' ],
            'code'                 => ( string ) $item[ 'code' ],
            'value'                => ( string ) $item[ 'value' ],
            'balance'              => ( string ) $item[ 'balance' ],
            'is_mail_sent'         => ( bool ) $item[ 'is_mail_sent' ],
            'is_instore'           => ( bool ) $item[ 'is_instore' ],
            'created_at'           => ( string ) GeneralHelperTools::formatDateTime( $item[ 'created_at' ] ),
            'delivery_date'        => ( string ) GeneralHelperTools::formatDateTime( $item[ 'delivery_date' ] ),
            'recipient_email'      => ( string ) $item[ 'recipient_email' ],
            'message'              => ( string ) $item[ 'message' ],
            'expiry_date'          => ( string ) GeneralHelperTools::formatDateTime( $item[ 'expiry_date' ] ),
            'validation_times'     => ( string ) $item[ 'validation_times' ],
            'last_validation_date' => ( string ) GeneralHelperTools::formatDateTime( $item[ 'last_validation_date' ] ),
            "merchant_name" => (string)$item['merchant_name'],
            'relations'            => [
                'voucher_parameter'       => $voucher_parameter,
                'order'                   => $order,
                'customer'                => $user,
                'voucher_validation_logs' => $voucher_validation_logs,
            ]
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }

}
