<?php

namespace App\EssentialEntities\Transformers;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LessonTransformer
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class GiftVoucherTransformer extends Transformer {

    public function transform(array $item) {
        return [
            'id' => (string)$item['id'],
            'voucher_code' => (string)$item['qr_code'],
            'gift_vouchers_parameters_id' => (string)$item['gift_vouchers_parameters_id'], // Merchant
            'customer_id' => (string)$item['customer_id'], // Customer
            'purchase_date' => (string)$item['created_at'],
            'voucher_value' => (string)$item['voucher_value'],
            'voucher_balance' => (string)$item['voucher_balance'],
            'validate_guarantee' => (string)'No',
            'last_validation' => (string)$item['updated_at'],
            'status' => (string)$item['status'],
            // voucher_title
            'recipient_email' => (string)$item['recipient_email'],
            'delivery_date' => (string)$item['delivery_date'],
            'expiry_date' => (string)$item['expiry_date'],
            // allowed_validations
            'validations_made' => (string)$item['used_times']
            // Validations_left
            // may be no need 'message' => $item['message'],
            // may be no need 'validation_date' => $item['validation_date'],
        ];
    }

    public function beforeStandard( array $item ) {
        
    }

}
