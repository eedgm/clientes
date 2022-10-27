<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payable;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayablePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the payable can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list payables');
    }

    /**
     * Determine whether the payable can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Payable  $model
     * @return mixed
     */
    public function view(User $user, Payable $model)
    {
        return $user->hasPermissionTo('view payables');
    }

    /**
     * Determine whether the payable can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create payables');
    }

    /**
     * Determine whether the payable can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Payable  $model
     * @return mixed
     */
    public function update(User $user, Payable $model)
    {
        return $user->hasPermissionTo('update payables');
    }

    /**
     * Determine whether the payable can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Payable  $model
     * @return mixed
     */
    public function delete(User $user, Payable $model)
    {
        return $user->hasPermissionTo('delete payables');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Payable  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete payables');
    }

    /**
     * Determine whether the payable can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Payable  $model
     * @return mixed
     */
    public function restore(User $user, Payable $model)
    {
        return false;
    }

    /**
     * Determine whether the payable can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Payable  $model
     * @return mixed
     */
    public function forceDelete(User $user, Payable $model)
    {
        return false;
    }
}
