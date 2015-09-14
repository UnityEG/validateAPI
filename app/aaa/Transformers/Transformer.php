<?php

namespace App\aaa\Transformers;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transformer
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
abstract class Transformer {

    public function transformCollection(array $items) {
        //
//        return  $items;
        return array_map([$this, 'transform'], $items);
    }

    public function transform($item){}

}
