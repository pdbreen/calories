<?php

namespace Tests\Feature;

use App\Meal;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\Support\MealHelper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MealApiTest extends TestCase
{
    use DatabaseTransactions, MealHelper;

    public function testGuestCannotShowMeal()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);
        $this->json('GET', "/api/meals/{$meal->id}")
            ->assertStatus(401);
    }

    public function testGuestCannotAddMeal()
    {
        $this->json('POST', "/api/meals", [
            'description' => 'Lunch',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(401);
    }

    public function testGuestCannotUpdateMeal()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);
        $this->json('PUT', "/api/meals/{$meal->id}", [
            'description' => 'Lunch',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(401);
    }

    public function testGuestCannotListMeals()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);
        $this->json('GET', "/api/meals")
            ->assertStatus(401);
    }

    public function testUserCanShowMeal()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);

        Passport::actingAs($user);

        $this->json('GET', "/api/meals/{$meal->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $meal->id]);
    }

    public function testUserCannotShowMissingMeal()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);

        Passport::actingAs($user);

        $this->json('GET', "/api/meals/9999999")
            ->assertStatus(404);
    }

    public function testUserCannotShowOthersMeal()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);

        $secondUser = factory(User::class)->create();
        $secondMeal = factory(Meal::class)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $this->json('GET', "/api/meals/{$secondMeal->id}")
            ->assertStatus(403);
    }

    public function testUserManagerCannotShowOthersMeal()
    {
        $user = factory(User::class)->state('user_manager')->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);

        $secondUser = factory(User::class)->create();
        $secondMeal = factory(Meal::class)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $this->json('GET', "/api/meals/{$secondMeal->id}")
            ->assertStatus(403);
    }

    public function testAdminCanShowOthersMeal()
    {
        $user = factory(User::class)->state('admin')->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);

        $secondUser = factory(User::class)->create();
        $secondMeal = factory(Meal::class)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $this->json('GET', "/api/meals/{$secondMeal->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $secondMeal->id]);
    }

    public function testUserCanAddMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->assertEquals(0, $user->meals->count());

        Passport::actingAs($user);

        $this->json('POST', "/api/meals", [
            'user_id' => $user->id,
            'description' => 'Lunch',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(201);

        $user->refresh();
        $this->assertEquals(1, $user->meals->count());
    }

    public function testUserCannotAddMealWithNoDescription()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->assertEquals(0, $user->meals->count());

        Passport::actingAs($user);

        $this->json('POST', "/api/meals", [
            'user_id' => $user->id,
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(422);

        $user->refresh();
        $this->assertEquals(0, $user->meals->count());
    }

    public function testUserCannotAddMealWithBadCalories()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->assertEquals(0, $user->meals->count());

        Passport::actingAs($user);

        $this->json('POST', "/api/meals", [
            'user_id' => $user->id,
            'description' => 'Lunch',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => -10,
        ])->assertStatus(422);

        $user->refresh();
        $this->assertEquals(0, $user->meals->count());
    }

    public function testUserCannotAddMealWithBadDate()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->assertEquals(0, $user->meals->count());

        Passport::actingAs($user);

        $this->json('POST', "/api/meals", [
            'user_id' => $user->id,
            'description' => 'Lunch',
            'eaten_at' => 'not a date',
            'calories' => 200,
        ])->assertStatus(422);

        $user->refresh();
        $this->assertEquals(0, $user->meals->count());
    }

    public function testUserCannotAddOthersMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->assertEquals(0, $user->meals->count());

        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('POST', "/api/meals", [
            'user_id' => $secondUser->id,
            'description' => 'Lunch',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(403);
    }

    public function testUserManagerCannotAddOthersMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->state('user_manager')->create();
        $this->assertEquals(0, $user->meals->count());

        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('POST', "/api/meals", [
            'user_id' => $secondUser->id,
            'description' => 'Lunch',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(403);
    }

    public function testAdminCanAddOthersMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->state('admin')->create();
        $this->assertEquals(0, $user->meals->count());

        $secondUser = factory(User::class)->create();
        $this->assertEquals(0, $secondUser->meals->count());

        Passport::actingAs($user);

        $this->json('POST', "/api/meals", [
            'user_id' => $secondUser->id,
            'description' => 'Lunch',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(201);

        $secondUser->refresh();
        $this->assertEquals(1, $secondUser->meals->count());
    }

    public function testUserCanUpdateOwnMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->assertEquals(0, $user->meals->count());

        Passport::actingAs($user);

        $this->json('POST', "/api/meals", [
            'user_id' => $user->id,
            'description' => 'Lunch',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(201);

        $user->refresh();
        $this->assertEquals(1, $user->meals->count());

        $meal = $user->meals->first();
        $this->assertEquals('Lunch', $meal->description);

        $this->json('PUT', "/api/meals/{$meal->id}", [
            'description' => 'Dinner',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(200);

        $user->refresh();
        $this->assertEquals(1, $user->meals->count());

        $meal = $user->meals->first();
        $this->assertEquals('Dinner', $meal->description);
    }

    public function testUserCannotUpdateOthersMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->assertEquals(0, $user->meals->count());

        $secondUser = factory(User::class)->create();
        $secondMeal = factory(Meal::class)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $this->json('PUT', "/api/meals/{$secondMeal->id}", [
            'description' => 'Dinner',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(403);
    }

    public function testUserManagerCannotUpdateOthersMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->state('user_manager')->create();
        $this->assertEquals(0, $user->meals->count());

        $secondUser = factory(User::class)->create();
        $secondMeal = factory(Meal::class)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $this->json('PUT', "/api/meals/{$secondMeal->id}", [
            'description' => 'Dinner',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(403);
    }

    public function testAdminCanUpdateOthersMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->state('admin')->create();
        $this->assertEquals(0, $user->meals->count());

        $secondUser = factory(User::class)->create();
        $secondMeal = factory(Meal::class)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $this->json('PUT', "/api/meals/{$secondMeal->id}", [
            'description' => 'Dinner',
            'eaten_at' => '2019-02-01 12:30:00',
            'calories' => 200,
        ])->assertStatus(200);

        $secondUser->refresh();
        $this->assertEquals(1, $secondUser->meals->count());

        $meal = $secondUser->meals->first();
        $this->assertEquals('Dinner', $meal->description);
    }

    public function testUserCanDeleteOwnMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);

        Passport::actingAs($user);

        $this->json('DELETE', "/api/meals/{$meal->id}")
            ->assertStatus(204);

        $user->refresh();
        $this->assertEquals(0, $user->meals->count());
    }

    public function testUserCannotDeleteOthersMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);

        $secondUser = factory(User::class)->create();
        $secondMeal = factory(Meal::class)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $this->json('DELETE', "/api/meals/{$secondMeal->id}")
            ->assertStatus(403);
    }

    public function testUserManagerCannotDeleteOthersMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->state('user_manager')->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
        ]);

        $secondUser = factory(User::class)->create();
        $secondMeal = factory(Meal::class)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $this->json('DELETE', "/api/meals/{$secondMeal->id}")
            ->assertStatus(403);
    }

    public function testAdminCanDeleteOthersMeal()
    {
        /** @var User $user */
        $user = factory(User::class)->state('admin')->create();
        $this->assertEquals(0, $user->meals->count());

        $secondUser = factory(User::class)->create();
        $secondMeal = factory(Meal::class)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $this->json('DELETE', "/api/meals/{$secondMeal->id}")
            ->assertStatus(204);

        $secondUser->refresh();
        $this->assertEquals(0, $secondUser->meals->count());
    }

    public function testUserCanListOwnMeals()
    {
        $user = factory(User::class)->create();
        factory(Meal::class, 5)->create([
            'user_id' => $user->id,
        ]);

        $secondUser = factory(User::class)->create();
        factory(Meal::class, 8)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertEquals(5, count($meals));
    }

    public function testUserManagerCanListOwnMeals()
    {
        $user = factory(User::class)->state('user_manager')->create();
        factory(Meal::class, 6)->create([
            'user_id' => $user->id,
        ]);

        $secondUser = factory(User::class)->create();
        factory(Meal::class, 8)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertEquals(6, count($meals));
    }

    public function testAdminCanListOwnMeals()
    {
        $user = factory(User::class)->state('admin')->create();
        factory(Meal::class, 7)->create([
            'user_id' => $user->id,
        ]);

        $secondUser = factory(User::class)->create();
        factory(Meal::class, 8)->create([
            'user_id' => $secondUser->id,
        ]);

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(7, count($meals));
    }

    public function testUserCanListMealsWithDateFilters()
    {
        $user = $this->createUserWithTimedMeals();

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(3, count($meals));

        $response = $this->json('GET', "/api/meals", [
            'start_date' => '2019-02-03'
        ])
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(1, count($meals));

        $response = $this->json('GET', "/api/meals", [
            'start_date' => '2019-01-31',
            'end_date' => '2019-02-02',
        ])
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(2, count($meals));
    }

    public function testUserCanListMealsWithTimeFilters()
    {
        $user = $this->createUserWithTimedMeals();

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(3, count($meals));

        $response = $this->json('GET', "/api/meals", [
            'start_time' => '2019-01-01 10:15:00'
        ])->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(2, count($meals));

        $response = $this->json('GET', "/api/meals", [
            'start_time' => '2019-01-01 05:40:00',
            'end_time' => '2019-01-01 12:30:00',
        ])->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(2, count($meals));
    }

    public function testUserCanListMealsWithDateAndTimeFilters()
    {
        $user = $this->createUserWithTimedMeals();

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(3, count($meals));

        $response = $this->json('GET', "/api/meals", [
            'start_time' => '2019-01-01 10:15:00',
            'end_date' => '2019-02-02'
        ])->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(1, count($meals));

        $response = $this->json('GET', "/api/meals", [
            'start_date' => '2019-02-02',
            'end_time' => '2019-01-01 12:30:00',
        ])->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(1, count($meals));
    }
}
