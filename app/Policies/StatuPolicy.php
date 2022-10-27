<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Statu;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatuPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the statu can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list status');
    }

    /**
     * Determine whether the statu can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Statu  $model
     * @return mixed
     */
    public function view(User $user, Statu $model)
    {
        return $user->hasPermissionTo('view status');
    }

    /**
     * Determine whether the statu can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create status');
    }

    /**
     * Determine whether the statu can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Statu  $model
     * @return mixed
     */
    public function update(User $user, Statu $model)
    {
        return $user->hasPermissionTo('update status');
    }

    /**
     * Determine whether the statu can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Statu  $model
     * @return mixed
     */
    public function delete(User $user, Statu $model)
    {
        return $user->hasPermissionTo('delete status');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Statu  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete status');
    }

    /**
     * Determine whether the statu can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Statu  $model
     * @return mixed
     */
    public function restore(User $user, Statu $model)
    {
        return false;
    }

    /**
     * Determine whether the statu can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Statu  $model
     * @return mixed
     */
    public function forceDelete(User $user, Statu $model)
    {
        return false;
    }
}
