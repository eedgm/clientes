<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the supplier can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list suppliers');
    }

    /**
     * Determine whether the supplier can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Supplier  $model
     * @return mixed
     */
    public function view(User $user, Supplier $model)
    {
        return $user->hasPermissionTo('view suppliers');
    }

    /**
     * Determine whether the supplier can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create suppliers');
    }

    /**
     * Determine whether the supplier can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Supplier  $model
     * @return mixed
     */
    public function update(User $user, Supplier $model)
    {
        return $user->hasPermissionTo('update suppliers');
    }

    /**
     * Determine whether the supplier can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Supplier  $model
     * @return mixed
     */
    public function delete(User $user, Supplier $model)
    {
        return $user->hasPermissionTo('delete suppliers');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Supplier  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete suppliers');
    }

    /**
     * Determine whether the supplier can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Supplier  $model
     * @return mixed
     */
    public function restore(User $user, Supplier $model)
    {
        return false;
    }

    /**
     * Determine whether the supplier can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Supplier  $model
     * @return mixed
     */
    public function forceDelete(User $user, Supplier $model)
    {
        return false;
    }
}
