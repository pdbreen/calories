<?php

use App\Meal;
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

$factory->define(Meal::class, function (Faker $faker) {
    return [
        'description' => $faker->text(),
        'eaten_at' => $faker->dateTimeBetween('-1 week', 'now')->setTime(
            $faker->numberBetween(6, 20),
            $faker->randomElement([0, 15, 30, 45])
        ),
        'calories' => $faker->numberBetween(200, 1500),
    ];
});

$factory->state(Meal::class, 'breakfast', function ($faker) {
    return [
        'eaten_at' => $faker->dateTimeBetween('-1 week', 'now')->setTime(
            $faker->numberBetween(Meal::BREAKFAST_START_HOUR, Meal::BREAKFAST_END_HOUR - 1),
            $faker->randomElement([0, 15, 30, 45])
        ),
    ];
});

$factory->state(Meal::class, 'lunch', function ($faker) {
    return [
        'eaten_at' => $faker->dateTimeBetween('-1 week', 'now')->setTime(
            $faker->numberBetween(Meal::LUNCH_START_HOUR, Meal::LUNCH_END_HOUR - 1),
            $faker->randomElement([0, 15, 30, 45])
        ),
    ];
});

$factory->state(Meal::class, 'dinner', function ($faker) {
    return [
        'eaten_at' => $faker->dateTimeBetween('-1 week', 'now')->setTime(
            $faker->numberBetween(Meal::DINNER_START_HOUR, Meal::DINNER_END_HOUR - 1),
            $faker->randomElement([0, 15, 30, 45])
        ),
    ];
});

$factory->state(Meal::class, 'snack', function ($faker) {
    return [
        'eaten_at' => $faker->dateTimeBetween('-1 week', 'now')->setTime(
            $faker->randomElement([9, 10, 14, 15, 16, 20, 21, 22]),
            $faker->randomElement([0, 15, 30, 45])
        ),
    ];
});
