<?php

namespace Tests\Unit;

use App\Meal;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Support\MealHelper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MealTest extends TestCase
{
    use DatabaseTransactions, MealHelper;

    public function testCanGetUser()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($meal->user);
        $this->assertEquals($user->id, $meal->user->id);
    }

    public function testCanScopeMealsByUser()
    {
        $user = $this->createUserWithFactoryMeals(5);
        $secondUser = $this->createUserWithFactoryMeals(8);

        $meals = Meal::query()
            ->byUser($user)
            ->get();

        $this->assertNotNull($meals);
        $this->assertEquals(5, $meals->count());

        $meals = Meal::query()
            ->byUser($secondUser)
            ->get();

        $this->assertNotNull($meals);
        $this->assertEquals(8, $meals->count());
    }

    public function testCanDetermineBreakfast()
    {
        $meal = factory(Meal::class)->state('breakfast')->make();
        $this->assertTrue($meal->is_breakfast);
        $this->assertFalse($meal->is_lunch);
        $this->assertFalse($meal->is_dinner);
        $this->assertFalse($meal->is_snack);
    }

    public function testCanDetermineLunch()
    {
        $meal = factory(Meal::class)->state('lunch')->make();
        $this->assertFalse($meal->is_breakfast);
        $this->assertTrue($meal->is_lunch);
        $this->assertFalse($meal->is_dinner);
        $this->assertFalse($meal->is_snack);
    }

    public function testCanDetermineDinner()
    {
        $meal = factory(Meal::class)->state('dinner')->make();
        $this->assertFalse($meal->is_breakfast);
        $this->assertFalse($meal->is_lunch);
        $this->assertTrue($meal->is_dinner);
        $this->assertFalse($meal->is_snack);
    }

    public function testCanDetermineSnack()
    {
        $meal = factory(Meal::class)->state('snack')->make();
        $this->assertFalse($meal->is_breakfast);
        $this->assertFalse($meal->is_lunch);
        $this->assertFalse($meal->is_dinner);
        $this->assertTrue($meal->is_snack);
    }

    public function testCanScopeMealsByStartDate()
    {
        $user = $this->createUserWithTimedMeals();

        $meals = Meal::query()->byUser($user)->byStartDate('2019-02-01')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(3, $meals->count());

        $meals = Meal::query()->byUser($user)->byStartDate(Carbon::create(2019, 2,2 ))->get();
        $this->assertNotNull($meals);
        $this->assertEquals(2, $meals->count());

        $meals = Meal::query()->byUser($user)->byStartDate('2019-03-01')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(0, $meals->count());
    }

    public function testCanScopeMealsByEndDate()
    {
        $user = $this->createUserWithTimedMeals();

        $meals = Meal::query()->byUser($user)->byEndDate('2019-02-03')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(3, $meals->count());

        $meals = Meal::query()->byUser($user)->byEndDate(Carbon::create(2019, 2,2 ))->get();
        $this->assertNotNull($meals);
        $this->assertEquals(2, $meals->count());

        $meals = Meal::query()->byUser($user)->byEndDate('2019-01-31')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(0, $meals->count());
    }

    public function testCanScopeMealsBetweenDates()
    {
        $user = $this->createUserWithTimedMeals();

        $meals = Meal::query()->byUser($user)->byStartDate('2019-02-01')->byEndDate('2019-02-02')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(2, $meals->count());

        $meals = Meal::query()->byUser($user)->byStartDate('2019-02-03')->byEndDate('2019-03-01')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(1, $meals->count());

        $meals = Meal::query()->byUser($user)->byStartDate('2019-03-03')->byEndDate('2019-04-01')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(0, $meals->count());
    }

    public function testCanScopeMealsByStartTime()
    {
        $user = $this->createUserWithTimedMeals();

        $meals = Meal::query()->byUser($user)->byStartTime('08:30')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(3, $meals->count());

        $meals = Meal::query()->byUser($user)->byStartTime(Carbon::createFromTime(11, 30))->get();
        $this->assertNotNull($meals);
        $this->assertEquals(2, $meals->count());

        $meals = Meal::query()->byUser($user)->byStartTime('20:00')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(0, $meals->count());
    }

    public function testCanScopeMealsByEndTime()
    {
        $user = $this->createUserWithTimedMeals();

        $meals = Meal::query()->byUser($user)->byEndTime('20:00')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(3, $meals->count());

        $meals = Meal::query()->byUser($user)->byEndTime(Carbon::createFromTime(12, 15))->get();
        $this->assertNotNull($meals);
        $this->assertEquals(2, $meals->count());

        $meals = Meal::query()->byUser($user)->byEndTime('05:30')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(0, $meals->count());
    }

    public function testCanScopeMealsBetweenTimes()
    {
        $user = $this->createUserWithTimedMeals();

        $meals = Meal::query()->byUser($user)->byStartTime('06:00')->byEndTime('10:00')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(1, $meals->count());

        $meals = Meal::query()->byUser($user)->byStartTime(Carbon::createFromTime(11, 30))->byEndTime('20:00')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(2, $meals->count());

        $meals = Meal::query()->byUser($user)->byStartTime('08:30')->byEndTime('21:00')->get();
        $this->assertNotNull($meals);
        $this->assertEquals(3, $meals->count());
    }

    public function testCanScopeMealsByDateAndTime()
    {
        $user = $this->createUserWithTimedMeals();

        $meals = Meal::query()->byUser($user)
            ->byStartDate('2019-02-01')
            ->byStartTime('06:00')->byEndTime('10:00')
            ->get();
        $this->assertNotNull($meals);
        $this->assertEquals(1, $meals->count());

        $meals = Meal::query()->byUser($user)
            ->byEndDate('2019-02-01')
            ->byStartTime(Carbon::createFromTime(11, 30))->byEndTime('20:00')
            ->get();
        $this->assertNotNull($meals);
        $this->assertEquals(0, $meals->count());

        $meals = Meal::query()->byUser($user)
            ->byEndDate('2019-02-03')
            ->byStartTime(Carbon::createFromTime(11, 30))
            ->get();
        $this->assertNotNull($meals);
        $this->assertEquals(2, $meals->count());

        $meals = Meal::query()->byUser($user)
            ->byStartDate('2019-02-01')->byEndDate('2019-02-03')
            ->byStartTime('08:30')->byEndTime('21:00')
            ->get();
        $this->assertNotNull($meals);
        $this->assertEquals(3, $meals->count());

        $meals = Meal::query()->byUser($user)
            ->byStartDate('2019-03-01')->byEndDate('2019-03-03')
            ->byStartTime('08:30')->byEndTime('21:00')
            ->get();
        $this->assertNotNull($meals);
        $this->assertEquals(0, $meals->count());

        $meals = Meal::query()->byUser($user)
            ->byStartDate('2019-02-01')->byEndDate('2019-02-03')
            ->byStartTime('21:30')->byEndTime('23:00')
            ->get();
        $this->assertNotNull($meals);
        $this->assertEquals(0, $meals->count());
    }

    public function testCanIncludeDailyCalorieTotals()
    {
        $user = factory(User::class)->create([
            'expected_calories' => 1000,
        ]);
        $user->meals()->saveMany(
            factory(Meal::class, 3)->make([
                'user_id' => $user->id,
                'eaten_at' => Carbon::create(2019, 2, 1),
                'calories' => 100
            ])
        );
        $user->meals()->saveMany(
            factory(Meal::class, 6)->make([
                'user_id' => $user->id,
                'eaten_at' => Carbon::create(2019, 2, 2),
                'calories' => 150
            ])
        );
        $user->meals()->saveMany(
            factory(Meal::class, 4)->make([
                'user_id' => $user->id,
                'eaten_at' => Carbon::create(2019, 2, 3),
                'calories' => 500
            ])
        );

        $meals = Meal::query()->byUser($user)
            ->withDailyCalorieTotals()
            ->get();

        $this->assertNotNull($meals);
        $this->assertEquals(13, $meals->count());

        $meal = $meals->first(function (Meal $meal) {
            return $meal->eaten_at->day === 1;
        });
        $this->assertNotNull($meal);
        $this->assertEquals(1000, $meal->expected_calories);
        $this->assertEquals(300, $meal->total_calories);

        $meal = $meals->first(function (Meal $meal) {
            return $meal->eaten_at->day === 2;
        });
        $this->assertNotNull($meal);
        $this->assertEquals(1000, $meal->expected_calories);
        $this->assertEquals(900, $meal->total_calories);

        $meal = $meals->first(function (Meal $meal) {
            return $meal->eaten_at->day === 3;
        });
        $this->assertNotNull($meal);
        $this->assertEquals(1000, $meal->expected_calories);
        $this->assertEquals(2000, $meal->total_calories);
    }
}
