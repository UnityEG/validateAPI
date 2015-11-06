<?php

namespace App\EssentialEntities\Transformers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of UserTransformer
 *
 * @author mohamed
 */
class UserTransformer extends Facade{
    protected static function getFacadeAccessor() {
        return 'UserTransformer';
    }
}
