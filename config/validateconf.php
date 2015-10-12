<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default absolute business logos path 
    |--------------------------------------------------------------------------
    |
    | use this path to store and delete business logos.
    |
    */
    'default_business_logos_path' => ($app->runningInConsole()) ? 'images/business/logos/' : public_path('images/business/logos/'),
    
    /*
    |--------------------------------------------------------------------------
    | Default relative business logos path 
    |--------------------------------------------------------------------------
    |
    | use this path to get business logos uri for showing.
    |
    */
    'default_business_logos_uri' => ($app->runningInConsole()) ?  'images/business/logos/' : asset('images/business/logos/')
];

