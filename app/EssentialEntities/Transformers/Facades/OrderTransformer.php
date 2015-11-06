<?php

namespace App\EssentialEntities\Transformers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of OrderTransformer
 *
 * @author Mohamed Atef < at gmail.com>
 */
class OrderTransformer extends Facade{
    public static function getFacadeAccessor() {
        return 'orderTransformer';
    }
}
