<?php

namespace Tests\Feature;

use App\Meal;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\Support\MealHelper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    use DatabaseTransactions;


    public function testGuestHasNoCurrentUser()
    {
        $user = factory(User::class)->create();
        $response = $this->json('GET', "/api/user")
            ->assertStatus(401);
    }

    public function testUserHasCurrentUser()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $response = $this->json('GET', "/api/user")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $user->id]);
    }

    public function testGuestCanLogin()
    {
        $user = factory(User::class)->create(['email' => 'testuser@example.com']);

        $this->json('POST', "/api/login", [
                'email' => $user->email,
                'password' => 'password'
            ])->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function testGuestCannotLoginWithoutPassword()
    {
        $user = factory(User::class)->create(['email' => 'testuser@example.com']);

        $this->json('POST', "/api/login", [
            'email' => $user->email,
        ])->assertStatus(422);
    }

    public function testGuestCannotLoginWithWrongPassword()
    {
        $user = factory(User::class)->create(['email' => 'testuser@example.com']);

        $this->json('POST', "/api/login", [
            'email' => $user->email,
            'password' => 'WRONG'
        ])->assertStatus(401);
    }

    public function testGuestCannotLoginWithBadEmail()
    {
        $user = factory(User::class)->create(['email' => 'testuser@example.com']);

        $this->json('POST', "/api/login", [
            'email' => 'not_valid_email',
            'password' => 'password'
        ])->assertStatus(422);
    }

    public function testGuestCannotLoginWithWrongEmail()
    {
        $user = factory(User::class)->create(['email' => 'testuser@example.com']);

        $this->json('POST', "/api/login", [
            'email' => 'incorrect@example.com',
            'password' => 'password'
        ])->assertStatus(401);
    }

    public function testGuestCanRegister()
    {
        $this->json('POST', "/api/register", [
            'name' => 'A Test',
            'email' => 'testuser@example.com',
            'password' => 'password'
        ])->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function testGuestCannotRegisterWithExistingEmail()
    {
        $user = factory(User::class)->create(['email' => 'testuser@example.com']);

        $this->json('POST', "/api/register", [
            'name' => 'A Test',
            'email' => 'testuser@example.com',
            'password' => 'password'
        ])->assertStatus(422);
    }

    public function testGuestCannotRegisterWithoutPassword()
    {
        $this->json('POST', "/api/register", [
            'name' => 'A Test',
            'email' => 'testuser@example.com',
        ])->assertStatus(422);
    }

    public function testGuestCannotRegisterWithBadEmail()
    {
        $this->json('POST', "/api/register", [
            'name' => 'A Test',
            'email' => 'not_valid_email',
            'password' => 'password'
        ])->assertStatus(422);
    }

    public function testGuestCannotShowUser()
    {
        $user = factory(User::class)->create();
        $this->json('GET', "/api/users/{$user->id}")
            ->assertStatus(401);
    }

    public function testGuestCannotAddUser()
    {
        $this->json('POST', "/api/users", [
            'name' => 'A User',
            'email' => 'auser@example.com',
            'expected_calories' => 1000,
        ])->assertStatus(401);
    }

    public function testGuestCannotUpdateUser()
    {
        $user = factory(User::class)->create();
        $this->json('PUT', "/api/users/{$user->id}", [
            'name' => 'A User',
            'email' => 'auser@example.com',
            'expected_calories' => 1000,
        ])->assertStatus(401);
    }

    public function testGuestCannotListUsers()
    {
        $user = factory(User::class)->create();
        $this->json('GET', "/api/users")
            ->assertStatus(401);
    }

    public function testGuestCannotListUsersMeals()
    {
        $user = factory(User::class)->create();
        $this->json('GET', "/api/users/{$user->id}/meals")
            ->assertStatus(401);
    }

    public function testUserCanShowSelf()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('GET', "/api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $user->id]);
    }

    public function testUserCannotShowMissingUser()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('GET', "/api/users/9999999")
            ->assertStatus(404);
    }

    public function testUserCannotShowOtherUser()
    {
        $user = factory(User::class)->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('GET', "/api/users/{$secondUser->id}")
            ->assertStatus(403);
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

        $response = $this->json('GET', "/api/users/{$user->id}/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertEquals(5, count($meals));
    }

    public function testUserCanListOthersMeals()
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

        $response = $this->json('GET', "/api/users/{$secondUser->id}/meals")
            ->assertStatus(403);
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

        $response = $this->json('GET', "/api/users/{$user->id}/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertEquals(6, count($meals));
    }

    public function testUserManagerCannotListOthersMeals()
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

        $response = $this->json('GET', "/api/users/{$secondUser->id}/meals")
            ->assertStatus(403);
    }

    public function testUserAdminCanListOwnMeals()
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

        $response = $this->json('GET', "/api/users/{$user->id}/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(7, count($meals));
    }

    public function testUserAdminCanListOthersMeals()
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

        $response = $this->json('GET', "/api/users/{$secondUser->id}/meals")
            ->assertStatus(200);

        $meals = $response->json('data');
        $this->assertNotNull($meals);
        $this->assertGreaterThanOrEqual(8, count($meals));
    }

    public function testUserManagerCanShowOtherUser()
    {
        $user = factory(User::class)->state('user_manager')->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('GET', "/api/users/{$secondUser->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $secondUser->id]);
    }

    public function testAdminCanShowOtherUser()
    {
        $user = factory(User::class)->state('admin')->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('GET', "/api/users/{$secondUser->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $secondUser->id]);
    }

    public function testUserCannotAddUser()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('POST', "/api/users", [
            'name' => 'My Name',
            'email' => 'myname@example.com',
            'password' => 'password',
            'expected_calories' => 1000,
            'role' => 1,
        ])->assertStatus(403);
    }

    public function testUserManagerCanAddUser()
    {
        /** @var User $user */
        $user = factory(User::class)->state('user_manager')->create();

        Passport::actingAs($user);

        $this->json('POST', "/api/users", [
            'name' => 'My Name',
            'email' => 'myname@example.com',
            'password' => 'password',
            'expected_calories' => 1000,
            'role' => 1,
        ])->assertStatus(201);

        $user = User::query()
            ->where('email', 'myname@example.com')
            ->first();
        $this->assertNotNull($user);
    }

    public function testAdminCanAddUser()
    {
        /** @var User $user */
        $user = factory(User::class)->state('admin')->create();

        Passport::actingAs($user);

        $this->json('POST', "/api/users", [
            'name' => 'My Name',
            'email' => 'myname@example.com',
            'password' => 'password',
            'expected_calories' => 1000,
            'role' => 1,
        ])->assertStatus(201);

        $user = User::query()
            ->where('email', 'myname@example.com')
            ->first();
        $this->assertNotNull($user);
    }

    public function testAdminCannotAddUserWithNoName()
    {
        /** @var User $user */
        $user = factory(User::class)->state('admin')->create();

        Passport::actingAs($user);

        $this->json('POST', "/api/users", [
            'email' => 'myname@example.com',
            'password' => 'password',
            'expected_calories' => 1000,
            'role' => 1,
        ])->assertStatus(422);
    }

    public function testAdminCannotAddUserWithBadEmail()
    {
        /** @var User $user */
        $user = factory(User::class)->state('admin')->create();

        Passport::actingAs($user);

        $this->json('POST', "/api/users", [
            'name' => 'My Name',
            'email' => 'not_a_valid_email',
            'password' => 'password',
            'expected_calories' => 1000,
            'role' => 1,
        ])->assertStatus(422);
    }

    public function testAdminCannotAddUserWithBadRole()
    {
        /** @var User $user */
        $user = factory(User::class)->state('admin')->create();

        Passport::actingAs($user);

        $this->json('POST', "/api/users", [
            'name' => 'My Name',
            'email' => 'myname@example.com',
            'password' => 'password',
            'expected_calories' => 1000,
            'role' => 99,
        ])->assertStatus(422);
    }

    public function testUserCanUpdateSelf()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('PUT', "/api/users/{$user->id}", [
            'name' => 'My Name',
            'email' => 'myname@example.com',
            'expected_calories' => 1000
        ])->assertStatus(200);

        $user->refresh();
        $this->assertEquals('My Name', $user->name);
        $this->assertEquals('myname@example.com', $user->email);
    }

    public function testUserCannotUpdateOtherUser()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('PUT', "/api/users/{$secondUser->id}", [
            'name' => 'My Name',
            'email' => 'myname@example.com',
            'expected_calories' => 1000
        ])->assertStatus(403);
    }

    public function testUserManagerCanUpdateOtherUser()
    {
        /** @var User $user */
        $user = factory(User::class)->state('user_manager')->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('PUT', "/api/users/{$secondUser->id}", [
            'name' => 'My Name',
            'email' => 'myname@example.com',
            'expected_calories' => 1000
        ])->assertStatus(200);

        $secondUser->refresh();
        $this->assertEquals('My Name', $secondUser->name);
        $this->assertEquals('myname@example.com', $secondUser->email);
    }

    public function testAdminCanUpdateOtherUser()
    {
        /** @var User $user */
        $user = factory(User::class)->state('admin')->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $this->json('PUT', "/api/users/{$secondUser->id}", [
            'name' => 'My Name',
            'email' => 'myname@example.com',
            'expected_calories' => 1000
        ])->assertStatus(200);

        $secondUser->refresh();
        $this->assertEquals('My Name', $secondUser->name);
        $this->assertEquals('myname@example.com', $secondUser->email);
    }

    public function testUserCanDeleteSelf()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/api/users/{$user->id}")
            ->assertStatus(204);

        $user = User::query()->find($user->id);
        $this->assertNull($user);
    }

    public function testUserCannotDeleteOtherUser()
    {
        $user = factory(User::class)->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/api/users/{$secondUser->id}")
            ->assertStatus(403);

    }

    public function testUserManagerCanDeleteOtherUser()
    {
        $user = factory(User::class)->state('user_manager')->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/api/users/{$secondUser->id}")
            ->assertStatus(204);

        $secondUser = User::query()->find($secondUser->id);
        $this->assertNull($secondUser);
    }

    public function testAdminCanDeleteOtherUser()
    {
        $user = factory(User::class)->state('admin')->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('DELETE', "/api/users/{$secondUser->id}")
            ->assertStatus(204);

        $secondUser = User::query()->find($secondUser->id);
        $this->assertNull($secondUser);
    }

    public function testUserCannotListUsers()
    {
        $user = factory(User::class)->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/users")
            ->assertStatus(403);
    }

    public function testUserManagerCanListUsers()
    {
        $user = factory(User::class)->state('user_manager')->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/users")
            ->assertStatus(200);

        $users = $response->json('data');
        $this->assertNotNull($users);
        $this->assertGreaterThanOrEqual(2,count($users));
    }

    public function testAdminCanListUsers()
    {
        $user = factory(User::class)->state('admin')->create();
        $secondUser = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/users")
            ->assertStatus(200);

        $users = $response->json('data');
        $this->assertNotNull($users);
        $this->assertGreaterThanOrEqual(2,count($users));
    }
}
