<?php

namespace App\aaa\Transformers;

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
class GiftVoucherValidationTransformer extends Transformer {

    public function transform($item) {
        return [
            'id' => (string)$item['id'],
            'giftvoucher_id' => (string)$item['giftvoucher_id'],
            'user_id' => (string)$item['user_id'], // Merchant
            'date' => (string)$item['created_at'], // Customer
            'value' => (string)$item['value'],
            'balance' => (string)$item['balance'],
            'log' => (string)$item['log']
        ];
    }

}
