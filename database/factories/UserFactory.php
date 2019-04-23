<?php

use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => 'password', //  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'expected_calories' => $faker->numberBetween(2500, 3500),
    ];
});

$factory->state(User::class, 'user_manager', [
    'role' => User::ROLE_USER_MANAGER,
    'expected_calories' => null,
]);

$factory->state(User::class, 'admin', [
    'role' => User::ROLE_ADMIN,
    'expected_calories' => null,
]);
