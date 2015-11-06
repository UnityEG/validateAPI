<?php

namespace App\EssentialEntities\Transformers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of VoucherTransformer
 *
 * @author mohamed
 */
class VoucherTransformer extends Facade{
    protected static function getFacadeAccessor() {
        return 'voucherTransformer';
    }
}
