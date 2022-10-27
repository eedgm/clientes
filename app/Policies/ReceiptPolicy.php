<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Receipt;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReceiptPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the receipt can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list receipts');
    }

    /**
     * Determine whether the receipt can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Receipt  $model
     * @return mixed
     */
    public function view(User $user, Receipt $model)
    {
        return $user->hasPermissionTo('view receipts');
    }

    /**
     * Determine whether the receipt can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create receipts');
    }

    /**
     * Determine whether the receipt can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Receipt  $model
     * @return mixed
     */
    public function update(User $user, Receipt $model)
    {
        return $user->hasPermissionTo('update receipts');
    }

    /**
     * Determine whether the receipt can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Receipt  $model
     * @return mixed
     */
    public function delete(User $user, Receipt $model)
    {
        return $user->hasPermissionTo('delete receipts');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Receipt  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete receipts');
    }

    /**
     * Determine whether the receipt can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Receipt  $model
     * @return mixed
     */
    public function restore(User $user, Receipt $model)
    {
        return false;
    }

    /**
     * Determine whether the receipt can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Receipt  $model
     * @return mixed
     */
    public function forceDelete(User $user, Receipt $model)
    {
        return false;
    }
}
