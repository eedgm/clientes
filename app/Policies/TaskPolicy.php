<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the task can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list tasks');
    }

    /**
     * Determine whether the task can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Task  $model
     * @return mixed
     */
    public function view(User $user, Task $model)
    {
        return $user->hasPermissionTo('view tasks');
    }

    /**
     * Determine whether the task can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create tasks');
    }

    /**
     * Determine whether the task can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Task  $model
     * @return mixed
     */
    public function update(User $user, Task $model)
    {
        return $user->hasPermissionTo('update tasks');
    }

    /**
     * Determine whether the task can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Task  $model
     * @return mixed
     */
    public function delete(User $user, Task $model)
    {
        return $user->hasPermissionTo('delete tasks');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Task  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete tasks');
    }

    /**
     * Determine whether the task can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Task  $model
     * @return mixed
     */
    public function restore(User $user, Task $model)
    {
        return false;
    }

    /**
     * Determine whether the task can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Task  $model
     * @return mixed
     */
    public function forceDelete(User $user, Task $model)
    {
        return false;
    }
}
