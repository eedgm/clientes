<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attach;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the attach can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list attaches');
    }

    /**
     * Determine whether the attach can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attach  $model
     * @return mixed
     */
    public function view(User $user, Attach $model)
    {
        return $user->hasPermissionTo('view attaches');
    }

    /**
     * Determine whether the attach can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create attaches');
    }

    /**
     * Determine whether the attach can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attach  $model
     * @return mixed
     */
    public function update(User $user, Attach $model)
    {
        return $user->hasPermissionTo('update attaches');
    }

    /**
     * Determine whether the attach can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attach  $model
     * @return mixed
     */
    public function delete(User $user, Attach $model)
    {
        return $user->hasPermissionTo('delete attaches');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attach  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete attaches');
    }

    /**
     * Determine whether the attach can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attach  $model
     * @return mixed
     */
    public function restore(User $user, Attach $model)
    {
        return false;
    }

    /**
     * Determine whether the attach can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attach  $model
     * @return mixed
     */
    public function forceDelete(User $user, Attach $model)
    {
        return false;
    }
}
