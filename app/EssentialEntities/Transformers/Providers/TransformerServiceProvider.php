<?php

namespace App\EssentialEntities\Transformers\Providers;

use App\EssentialEntities\Transformers\BusinessTransformer;
use App\EssentialEntities\Transformers\IndustryTransformer;
use App\EssentialEntities\Transformers\OrderTransformer;
use App\EssentialEntities\Transformers\RegionTransformer;
use App\EssentialEntities\Transformers\UserTransformer;
use App\EssentialEntities\Transformers\UseTermTransformer;
use App\EssentialEntities\Transformers\VoucherImageTransformer;
use App\EssentialEntities\Transformers\VoucherParametersTransformer;
use App\EssentialEntities\Transformers\VoucherTransformer;
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
        $this->voucherParametersTransformer();
        $this->orderTransformer();
        $this->useTermTransformer();
        $this->voucherImageTransformer();
        $this->regionTransformer();
        $this->industryTransformer();
    }
    
    protected function voucherValidationLogTransformer(){
        $this->app->singleton('VoucherValidationLogTransformer', function($app){
            return new VoucherValidationLogTransformer();
        });
    }
    
    protected function userTransformer(){
        $this->app->singleton('UserTransformer', function($app){
            return new UserTransformer();
        });
    }

    protected function businessTransformer() {
        $this->app->singleton('BusinessTransformer', function ($app){
            return new BusinessTransformer();
        });
    }

    protected function voucherTransformer() {
        $this->app->singleton('voucherTransformer', function($app){
            return new VoucherTransformer();
        });
    }
    
    protected function voucherParametersTransformer() {
        $this->app->singleton('voucherParametersTransformer', function($app){
            return new VoucherParametersTransformer();
        });
    }

    public function orderTransformer() {
        $this->app->singleton('orderTransformer', function($app){
            return new OrderTransformer();
        });
    }

    public function useTermTransformer() {
        $this->app->singleton('useTermTransformer', function($app){
            return new UseTermTransformer();
        });
    }

    public function voucherImageTransformer() {
        $this->app->singleton('voucherImageTransformer', function($app){
            return new VoucherImageTransformer();
        });
    }
    
    public function regionTransformer(){
        $this->app->singleton('RegionTransformer', function($app){
            return new RegionTransformer();
        });
    }
    
    public function industryTransformer(){
        $this->app->singleton('IndustryTransformer', function($app){
            return new IndustryTransformer();
        });
    }
}
