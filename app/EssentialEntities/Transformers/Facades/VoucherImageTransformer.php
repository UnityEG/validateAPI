<?php

namespace App\EssentialEntities\Transformers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of VoucherImageTransformer
 *
 * @author Mohamed Atef < at gmail.com>
 */
class VoucherImageTransformer extends Facade{
    public static function getFacadeAccessor() {
        return 'voucherImageTransformer';
    }
}
