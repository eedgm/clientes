<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Version;
use Illuminate\Auth\Access\HandlesAuthorization;

class VersionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the version can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list versions');
    }

    /**
     * Determine whether the version can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Version  $model
     * @return mixed
     */
    public function view(User $user, Version $model)
    {
        return $user->hasPermissionTo('view versions');
    }

    /**
     * Determine whether the version can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create versions');
    }

    /**
     * Determine whether the version can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Version  $model
     * @return mixed
     */
    public function update(User $user, Version $model)
    {
        return $user->hasPermissionTo('update versions');
    }

    /**
     * Determine whether the version can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Version  $model
     * @return mixed
     */
    public function delete(User $user, Version $model)
    {
        return $user->hasPermissionTo('delete versions');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Version  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete versions');
    }

    /**
     * Determine whether the version can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Version  $model
     * @return mixed
     */
    public function restore(User $user, Version $model)
    {
        return false;
    }

    /**
     * Determine whether the version can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Version  $model
     * @return mixed
     */
    public function forceDelete(User $user, Version $model)
    {
        return false;
    }
}
