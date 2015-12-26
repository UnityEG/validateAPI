<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

//User Factory
$factory->define(App\User::class, function (Faker\Generator $faker) {
    $essential = ['id'=>1, 'created_at'=>NULL, 'updated_at'=>NULL];
    return [
        'city_id' => factory(App\Http\Models\City::class)->make($essential)->id,
        'region_id' => factory(App\Http\Models\Region::class)->make($essential)->id,
        'town_id' => factory(App\Http\Models\Town::class)->make($essential)->id,
        'postcode_id' => factory(App\Http\Models\Postcode::class)->make($essential)->id,
        'facebook_user_id' => $faker->uuid,
        'is_active' => 1,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
        'title' => $faker->title,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'gender' => $faker->randomElement( ['male', 'female']),
        'dob' => $faker->dateTimeBetween( '-60 years', '-18 years'),
        'address1' => $faker->address,
        'address2' => $faker->address,
        'phone' => $faker->phoneNumber,
        'mobile' => $faker->phoneNumber,
        'is_notify_deal' => 0,
    ];
});

//Business Factory
$factory->define(App\Http\Models\Business::class, function(Faker\Generator $faker){
    $essential = ['id'=>1, 'created_at'=>NULL, 'updated_at'=>NULL];
    return [
        'logo_id' => 1,
        'city_id' => factory( App\Http\Models\City::class)->make($essential)->id,
        'region_id' => factory( App\Http\Models\Region::class)->make($essential)->id,
        'town_id' => factory(App\Http\Models\Region::class)->make($essential)->id,
        'postcode_id' => factory( App\Http\Models\Postcode::class)->make($essential)->id,
        'industry_id' => factory( App\Http\Models\Industry::class)->make($essential)->id,
        'facebook_page_id' => $faker->uuid,
        'code' => $faker->numberBetween( 10000000, 99999999),
        'is_new' => 0,
        'is_active' => 1,
        'is_display' => 1,
        'is_featured' => 0,
        'business_name' => $faker->company,
        'trading_name' => $faker->companySuffix,
        'bank_account_number' => $faker->creditCardNumber(),
        'address1' => $faker->address,
        'address2' => $faker->address,
        'phone' => $faker->phoneNumber,
        'website' => $faker->domainName,
        'business_email' => $faker->email,
        'contact_name' => $faker->name,
        'contact_mobile' => $faker->phoneNumber,
        'available_hours_mon' => 'Monday: 10:00am - 21:00pm',
        'available_hours_tue' => 'Tuesday: 10:00am - 21:00pm',
        'available_hours_wed' => 'Wednesday: 10:00am - 21:00pm',
        'available_hours_thu' => 'Thursday: 10:00am - 21:00pm',
        'available_hours_fri' => 'Friday: 10:00am - 21:00pm',
        'available_hours_sat' => 'Saturday: 10:00am - 21:00pm',
        'available_hours_sun' => 'Sunday: 10:00am - 21:00pm',
        'created_by' => factory(App\User::class)->make($essential)->id,
    ];
});

//BusinessLogo Factory
$factory->define( App\Http\Models\BusinessLogo::class, function(Faker\Generator $faker){
    return [
        'business_id' => $faker->numberBetween( 1, 100),
        'user_id' => factory(App\User::class)->make(['id'=>1])->id,
        'name' => $faker->numberBetween( 10000000, 99999999),
    ];
    
});

//VoucherParameter Factory
$factory->define( App\Http\Models\VoucherParameter::class, function(Faker\Generator $faker){
    return[
        'business_id' => factory(\App\Http\Models\Business::class)->create()->id,
        'user_id' => factory(\App\User::class)->create()->id,
        'voucher_image_id' => factory( \App\Http\Models\VoucherImage::class)->create()->id,
        'voucher_type' => 'gift',
        'title' => $faker->title,
        'purchase_start' => $faker->dateTime,
        'purchase_expiry' => $faker->dateTime,
        'is_expire' => 0,
        'is_display' => 1,
        'is_purchased' => 0,
        'valid_from' => $faker->dateTime,
        'valid_for_amount' => $faker->numberBetween(),
        'valid_for_units' => $faker->randomElement( ['h', 'd', 'w', 'm']),
        'valid_until' => $faker->dateTime,
        'is_limited_quantity' => 0,
        'quantity' => 0,
        'purchased_quantity' => 0,
        'stock_quantity' => 0,
        'short_description' => $faker->sentence,
        'long_description' => $faker->paragraph,
        'is_single_use' => 0,
        'no_of_uses' => 3,
        'retail_value' => 0,
        'value' => 0,
        'min_value' => 20.00,
        'max_value' => 255.56,
        'is_valid_during_month' => 0,
        'discount_percentage' => 0
    ];
});

//VoucherImage Factory
$factory->define( App\Http\Models\VoucherImage::class, function(Faker\Generator $faker){
    return[
        'name' => $faker->numberBetween( 10000001, 99999999),
        'voucher_type' => 'gift',
    ];
});

//UseTerm Factory
$factory->define( App\Http\Models\UseTerm::class, function(Faker\Generator $faker){
    return [
        'name' => $faker->word,
        'list_order' => $faker->numberBetween( 1, 5),
    ];
});

//Voucher Factory
$factory->define( App\Http\Models\Voucher::class, function(Faker\Generator $faker){
    return[
        'user_id' => factory(\App\User::class)->create()->id,
        'voucher_parameter_id' => factory( \App\Http\Models\VoucherParameter::class)->create()->id,
        'order_id' => factory(\App\Http\Models\Order::class)->create()->id,
        'status' => 'valid',
        'code' => $faker->numberBetween( 300000001, 399999999),
        'value' => $faker->numberBetween( 20.00, 550.59),
        'balance' => $faker->numberBetween( 20.00, 550.59),
        'is_mail_sent' => 0,
        'is_instore' => 0,
        'delivery_date' => $faker->dateTime,
        'recipient_email' => $faker->email,
        'message' => $faker->paragraph,
        'expiry_date' => $faker->dateTime,
        'validation_times' => 1,
        'last_validation_date' => $faker->dateTime
    ];
});

//Order Factory
$factory->define( App\Http\Models\Order::class, function(Faker\Generator $faker){
    return[
        'user_id' => factory(\App\User::class)->create()->id,
        'number' => $faker->numberBetween( 1001 ),
        'tax' => $faker->randomFloat(NULL, 0, 0.9),
    ];
});

//Region Factory
$factory->define( App\Http\Models\Region::class, function (Faker\Generator $faker){
    return[
        'region' => $faker->city
    ];
});

//City Factory
$factory->define( App\Http\Models\City::class, function(Faker\Generator $faker){
    return[
        'nz_city' => $faker->city
    ];
});

//Town Factory
$factory->define( App\Http\Models\Town::class, function(Faker\Generator $faker){
    return [
        'nz_town' => $faker->city
    ];
});

//Postcode Factory
$factory->define( App\Http\Models\Postcode::class, function(Faker\Generator $faker){
    return[
        'postcode' => $faker->postcode
    ];
});

//Industry Factory
$factory->define( App\Http\Models\Industry::class, function(Faker\Generator $faker){
    return[
        'industry' => $faker->word
    ];
});