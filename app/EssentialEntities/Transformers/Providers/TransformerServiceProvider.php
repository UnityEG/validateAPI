<?php

namespace App\EssentialEntities\Transformers\Providers;

use App\EssentialEntities\Transformers\VoucherValidationLogTransformer;
use Illuminate\Support\ServiceProvider;

/**
 * Description of TransformerServiceProvider
 *
 * @author mohamed
 */
class TransformerServiceProvider extends ServiceProvider{
    public function register() {
        $this->voucherValidationLogTransformer();
        $this->userTransformer();
        $this->businessTransformer();
        $this->voucherTransformer();
    }
    
    protected function voucherValidationLogTransformer(){
        $this->app->singleton('VoucherValidationLogTransformer', function($app){
            return new VoucherValidationLogTransformer();
        });
    }
    
    protected function userTransformer(){
        $this->app->singleton('UserTransformer', function($app){
            return new \App\EssentialEntities\Transformers\UserTransformer();
        });
    }

    protected function businessTransformer() {
        $this->app->singleton('BusinessTransformer', function ($app){
            return new \App\EssentialEntities\Transformers\BusinessTransformer();
        });
    }

    public function voucherTransformer() {
        $this->app->singleton('voucherTransformer', function($app){
            return new \App\EssentialEntities\Transformers\VoucherTransformer();
        });
    }

}
