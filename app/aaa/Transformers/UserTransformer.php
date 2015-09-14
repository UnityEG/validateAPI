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
class UserTransformer extends Transformer {

    public function transform($item) {
        return [
            'id' => (string)$item['id'],
            'user_type' => (string)$item['user_type'],
            'email' => (string)$item['email'],
            'active' => (boolean) $item['active'],
        ];
    }

}
