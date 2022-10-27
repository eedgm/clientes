<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Priority;
use Illuminate\Auth\Access\HandlesAuthorization;

class PriorityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the priority can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list priorities');
    }

    /**
     * Determine whether the priority can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Priority  $model
     * @return mixed
     */
    public function view(User $user, Priority $model)
    {
        return $user->hasPermissionTo('view priorities');
    }

    /**
     * Determine whether the priority can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create priorities');
    }

    /**
     * Determine whether the priority can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Priority  $model
     * @return mixed
     */
    public function update(User $user, Priority $model)
    {
        return $user->hasPermissionTo('update priorities');
    }

    /**
     * Determine whether the priority can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Priority  $model
     * @return mixed
     */
    public function delete(User $user, Priority $model)
    {
        return $user->hasPermissionTo('delete priorities');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Priority  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete priorities');
    }

    /**
     * Determine whether the priority can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Priority  $model
     * @return mixed
     */
    public function restore(User $user, Priority $model)
    {
        return false;
    }

    /**
     * Determine whether the priority can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Priority  $model
     * @return mixed
     */
    public function forceDelete(User $user, Priority $model)
    {
        return false;
    }
}
