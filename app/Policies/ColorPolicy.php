<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Color;
use Illuminate\Auth\Access\HandlesAuthorization;

class ColorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the color can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list colors');
    }

    /**
     * Determine whether the color can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Color  $model
     * @return mixed
     */
    public function view(User $user, Color $model)
    {
        return $user->hasPermissionTo('view colors');
    }

    /**
     * Determine whether the color can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create colors');
    }

    /**
     * Determine whether the color can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Color  $model
     * @return mixed
     */
    public function update(User $user, Color $model)
    {
        return $user->hasPermissionTo('update colors');
    }

    /**
     * Determine whether the color can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Color  $model
     * @return mixed
     */
    public function delete(User $user, Color $model)
    {
        return $user->hasPermissionTo('delete colors');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Color  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete colors');
    }

    /**
     * Determine whether the color can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Color  $model
     * @return mixed
     */
    public function restore(User $user, Color $model)
    {
        return false;
    }

    /**
     * Determine whether the color can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Color  $model
     * @return mixed
     */
    public function forceDelete(User $user, Color $model)
    {
        return false;
    }
}
