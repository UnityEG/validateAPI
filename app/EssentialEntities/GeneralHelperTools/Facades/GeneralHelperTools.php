<?php

namespace App\EssentialEntities\GeneralHelperTools\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of GeneralHelperTools
 *
 * @author mohamed
 */
class GeneralHelperTools extends Facade{
    
    /**
     * Getting register name of component to use in service container
     * @return string
     */
    protected static function getFacadeAccessor( ) {
        return 'generalHelperTools';
    }
}
