<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Developer;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeveloperPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the developer can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list developers');
    }

    /**
     * Determine whether the developer can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Developer  $model
     * @return mixed
     */
    public function view(User $user, Developer $model)
    {
        return $user->hasPermissionTo('view developers');
    }

    /**
     * Determine whether the developer can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create developers');
    }

    /**
     * Determine whether the developer can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Developer  $model
     * @return mixed
     */
    public function update(User $user, Developer $model)
    {
        return $user->hasPermissionTo('update developers');
    }

    /**
     * Determine whether the developer can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Developer  $model
     * @return mixed
     */
    public function delete(User $user, Developer $model)
    {
        return $user->hasPermissionTo('delete developers');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Developer  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete developers');
    }

    /**
     * Determine whether the developer can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Developer  $model
     * @return mixed
     */
    public function restore(User $user, Developer $model)
    {
        return false;
    }

    /**
     * Determine whether the developer can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Developer  $model
     * @return mixed
     */
    public function forceDelete(User $user, Developer $model)
    {
        return false;
    }
}
