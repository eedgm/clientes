<?php

namespace App\Policies;

use App\Models\Icon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IconPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the icon can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list icons');
    }

    /**
     * Determine whether the icon can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Icon  $model
     * @return mixed
     */
    public function view(User $user, Icon $model)
    {
        return $user->hasPermissionTo('view icons');
    }

    /**
     * Determine whether the icon can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create icons');
    }

    /**
     * Determine whether the icon can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Icon  $model
     * @return mixed
     */
    public function update(User $user, Icon $model)
    {
        return $user->hasPermissionTo('update icons');
    }

    /**
     * Determine whether the icon can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Icon  $model
     * @return mixed
     */
    public function delete(User $user, Icon $model)
    {
        return $user->hasPermissionTo('delete icons');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Icon  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete icons');
    }

    /**
     * Determine whether the icon can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Icon  $model
     * @return mixed
     */
    public function restore(User $user, Icon $model)
    {
        return false;
    }

    /**
     * Determine whether the icon can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Icon  $model
     * @return mixed
     */
    public function forceDelete(User $user, Icon $model)
    {
        return false;
    }
}
