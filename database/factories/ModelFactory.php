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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'city_id' => factory(App\Http\Models\City::class)->make(),
        'region_id' => factory(App\Http\Models\Region::class)->make(),
        'town_id' => factory(App\Http\Models\Town::class)->make(),
        'postcode_id' => factory(App\Http\Models\Postcode::class)->make(),
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
    return [
        'logo_id' => 1,
        'city_id' => factory( App\Http\Models\City::class)->create()->id,
        'region_id' => factory( App\Http\Models\Region::class)->create()->id,
        'town_id' => factory(App\Http\Models\Region::class)->create()->id,
        'postcode_id' => factory( App\Http\Models\Postcode::class)->create()->id,
        'industry_id' => factory( App\Http\Models\Industry::class)->create()->id,
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
        'created_by' => factory(App\User::class)->create()->id,
    ];
});

//BusinessLogo Factory
$factory->define( App\Http\Models\BusinessLogo::class, function(Faker\Generator $faker){
    return [
        'business_id' => $faker->numberBetween( 1, 100),
        'user_id' => factory(App\User::class)->create()->id,
        'name' => $faker->numberBetween( 10000000, 99999999),
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