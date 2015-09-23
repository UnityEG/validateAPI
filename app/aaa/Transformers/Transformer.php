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
    
    /**
     * Transform arrays to standard JSONAPI
     * @param array $items
     * @return Json
     */
    public function transformCollection(array $items) {
        $array_collection = ["data" =>array_map([$this, 'beforeStandard'], $items)];
        return $array_collection;
    }
    
    /**
     * Prepare and return with data before applying Json API standard
     * @param array $item
     */
    public function beforeStandard( $item) {
        
    }
    
    /**
     * Get data ready for send with Json API standard
     * @param array $item
     */
    public function transform($item){}

}
