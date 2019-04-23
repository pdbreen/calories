<?php

namespace Tests\Unit;

use App\Meal;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testCanGetMeals()
    {
        $user = factory(User::class)->create();
        $user = $user->fresh();
        $this->assertNotNull($user->meals);
        $this->assertEquals(0, $user->meals->count());

        $user->meals()->saveMany(
            factory(Meal::class, 15)->make()
        );

        $user = $user->fresh();
        $this->assertNotNull($user->meals);
        $this->assertEquals(15, $user->meals->count());
    }

    public function testCanDetermineAdminRole()
    {
        $user = factory(User::class)->state('admin')->make();
        $this->assertTrue($user->is_admin);
        $this->assertFalse($user->is_user_manager);
    }

    public function testCanDetermineUserManagerRole()
    {
        $user = factory(User::class)->state('user_manager')->make();
        $this->assertFalse($user->is_admin);
        $this->assertTrue($user->is_user_manager);
    }

    public function testCanDetermineUserRole()
    {
        $user = factory(User::class)->make();
        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->is_user_manager);
    }
}
