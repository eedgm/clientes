<?php

namespace App\Policies;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the rol can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list rols');
    }

    /**
     * Determine whether the rol can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Rol  $model
     * @return mixed
     */
    public function view(User $user, Rol $model)
    {
        return $user->hasPermissionTo('view rols');
    }

    /**
     * Determine whether the rol can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create rols');
    }

    /**
     * Determine whether the rol can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Rol  $model
     * @return mixed
     */
    public function update(User $user, Rol $model)
    {
        return $user->hasPermissionTo('update rols');
    }

    /**
     * Determine whether the rol can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Rol  $model
     * @return mixed
     */
    public function delete(User $user, Rol $model)
    {
        return $user->hasPermissionTo('delete rols');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Rol  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete rols');
    }

    /**
     * Determine whether the rol can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Rol  $model
     * @return mixed
     */
    public function restore(User $user, Rol $model)
    {
        return false;
    }

    /**
     * Determine whether the rol can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Rol  $model
     * @return mixed
     */
    public function forceDelete(User $user, Rol $model)
    {
        return false;
    }
}
