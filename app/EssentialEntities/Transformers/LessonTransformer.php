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
class LessonTransformer extends Transformer {

    public function transform(array $lesson) {
        return [
            'id' => $lesson['id'],
            'title' => $lesson['title'],
            'body' => $lesson['body'],
            'active' => (boolean) $lesson['display'],
        ];
    }

    public function beforeStandard( array $item ) {
        
    }

}
