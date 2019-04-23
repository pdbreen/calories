<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Meal;
use App\User;
use Carbon\Carbon;

trait MealHelper
{
    protected function createUserWithTimedMeals()
    {
        $user = factory(User::class)->create();
        factory(Meal::class)->create([
            'user_id' => $user->id,
            'eaten_at' => Carbon::create(2019, 2, 1, 8, 30)
        ]);
        factory(Meal::class)->create([
            'user_id' => $user->id,
            'eaten_at' => Carbon::create(2019, 2, 2, 12, 15)
        ]);
        factory(Meal::class)->create([
            'user_id' => $user->id,
            'eaten_at' => Carbon::create(2019, 2, 3, 18, 45)
        ]);
        return $user;
    }

    protected function createUserWithFactoryMeals(int $mealCount = 10)
    {
        $user = factory(User::class)->create();
        $user->meals()->saveMany(factory(Meal::class, $mealCount)->make());
        return $user;
    }
}