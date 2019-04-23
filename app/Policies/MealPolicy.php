<?php

namespace App\Policies;

use App\User;
use App\Meal;
use Illuminate\Auth\Access\HandlesAuthorization;

class MealPolicy
{
    use HandlesAuthorization;

    /**
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->is_admin) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function view(User $user, Meal $meal)
    {
        return $this->checkPermissions($user, $meal);
    }

    /**
     * Determine whether the user can create meals.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user ? true : false;
    }

    /**
     * Determine whether the user can update the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function update(User $user, Meal $meal)
    {
        return $this->checkPermissions($user, $meal);
    }

    /**
     * Determine whether the user can delete the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function delete(User $user, Meal $meal)
    {
        return $this->checkPermissions($user, $meal);
    }

    /**
     * Determine whether the user can restore the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function restore(User $user, Meal $meal)
    {
        return $this->checkPermissions($user, $meal);
    }

    /**
     * Determine whether the user can permanently delete the meal.
     *
     * @param  \App\User  $user
     * @param  \App\Meal  $meal
     * @return mixed
     */
    public function forceDelete(User $user, Meal $meal)
    {
        return $this->checkPermissions($user, $meal);
    }

    /**
     * Common permissions check
     *
     * @param User $user
     * @param Meal $meal
     * @return bool
     */
    private function checkPermissions(User $user, Meal $meal)
    {
        return $user->id == $meal->user_id;
    }
}
