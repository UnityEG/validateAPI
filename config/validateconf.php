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
    'default_business_logos_path' => ($app->runningInConsole()) ? '/home/mohamed/www/validateapi/public/images/business/logos/' : public_path('images/business/logos/'),
    
    /*
    |--------------------------------------------------------------------------
    | Default relative business logos path 
    |--------------------------------------------------------------------------
    |
    | use this path to get business logos URL for showing.
    |
    */
    'default_business_logos_uri' => ($app->runningInConsole()) ?  'http://www.validateapi.dev/public/images/business/logos/' : asset('images/business/logos/'),
    
    /*
    |--------------------------------------------------------------------------
    | Default absoulte voucher images path 
    |--------------------------------------------------------------------------
    |
    | use this path to store and delete voucher images.
    |
    */
    'default_voucher_images_path' => ($app->runningInConsole()) ? 'voucher/images/default' : public_path('voucher/images/default'),
    
    /*
    |--------------------------------------------------------------------------
    | Default relative voucher images path 
    |--------------------------------------------------------------------------
    |
    | use this path to get voucher images URL for showing.
    |
    */
    'default_voucher_images_uri' => ($app->runningInConsole()) ? 'voucher/images/default' : asset('voucher/images/default'),
    
    /*
    |--------------------------------------------------------------------------
    | Default relative voucher images path 
    |--------------------------------------------------------------------------
    |
    | use this path to get voucher images URL for showing.
    |
    */
    'default_virtual_vouchers_images_path' => ($app->runningInConsole()) ? '/home/mohamed/www/validateapi/storage/VirtualVouchers/' : storage_path('VirtualVouchers/'),
];

