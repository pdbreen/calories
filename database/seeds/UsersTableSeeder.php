<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create known admin user
        factory(App\User::class)->state('admin')->create([
            'email' => 'admin@calories.com',
        ]);

        // Create known user manager user
        factory(App\User::class)->state('user_manager')->create([
            'email' => 'user_manager@calories.com',
        ]);

        // Create known user
        $user = factory(App\User::class)->create([
            'email' => 'user@calories.com',
            'expected_calories' => '2500',
        ]);
        $user->meals()->saveMany(factory(App\Meal::class, 20)->make());

        factory(App\User::class, 5)->create()->each(function (User $user) {
            $user->meals()->saveMany(factory(App\Meal::class, 20)->make());
        });
    }
}
