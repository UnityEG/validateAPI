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
            'last_validation_date' => (string)GeneralHelperTools::formatDateTime($item['last_validation_date'])
        ];
        return $response;
    }

    public function transform( array $item ) {
        return ["data"=>  $this->beforeStandard($item)];
    }

}
