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
//        todo create relations of voucher object
        $voucher_parameter = (empty($item['voucher_parameter'])) ? ["data"=>["voucher_parameter_id"=>(string)$item['voucher_parameter_id']]] : $item['voucher_parameter'];
        $order = (empty($item['order'])) ? ["data"=>["order_id"=>(string)$item['order_id']]] : $item['order'];
        $user = (empty($item['user'])) ? ["data"=>["user_id"=>(string)$item['user_id']]] : $item['user'];
        $voucher_validation_logs = (empty($item['voucher_validation_logs'])) ? ["data"=>[]] : $item['voucher_validation_logs'];
        $response = [
            'id' => (string)$item['id'],
            'status' => $item['status'],
            'code' => (string)$item['code'],
            'value' => (string)$item['value'],
            'balance' => (string)$item['balance'],
            'is_gift' => (bool)$item['is_gift'],
            'is_instore' => (bool)$item['is_instore'],
            'delivery_date' => (string)GeneralHelperTools::formatDateTime($item['delivery_date']),
            'recipient_email' => (string)$item['recipient_email'],
            'message' => (string)$item['message'],
            'expiry_date' => (string)GeneralHelperTools::formatDateTime($item['expiry_date']),
            'validation_times' => (string)$item['validation_times'],
            'last_validation_date' => (string)GeneralHelperTools::formatDateTime($item['last_validation_date']),
            'relations' => [
                'voucher_parameter' => $voucher_parameter,
                'order' => $order,
                'user' => $user,
                'voucher_validation_logs' => $voucher_validation_logs,
            ]
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }

}
