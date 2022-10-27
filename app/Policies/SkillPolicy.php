<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Skill;
use Illuminate\Auth\Access\HandlesAuthorization;

class SkillPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the skill can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list skills');
    }

    /**
     * Determine whether the skill can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Skill  $model
     * @return mixed
     */
    public function view(User $user, Skill $model)
    {
        return $user->hasPermissionTo('view skills');
    }

    /**
     * Determine whether the skill can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create skills');
    }

    /**
     * Determine whether the skill can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Skill  $model
     * @return mixed
     */
    public function update(User $user, Skill $model)
    {
        return $user->hasPermissionTo('update skills');
    }

    /**
     * Determine whether the skill can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Skill  $model
     * @return mixed
     */
    public function delete(User $user, Skill $model)
    {
        return $user->hasPermissionTo('delete skills');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Skill  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete skills');
    }

    /**
     * Determine whether the skill can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Skill  $model
     * @return mixed
     */
    public function restore(User $user, Skill $model)
    {
        return false;
    }

    /**
     * Determine whether the skill can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Skill  $model
     * @return mixed
     */
    public function forceDelete(User $user, Skill $model)
    {
        return false;
    }
}
