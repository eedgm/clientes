<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attachment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the attachment can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list attachments');
    }

    /**
     * Determine whether the attachment can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attachment  $model
     * @return mixed
     */
    public function view(User $user, Attachment $model)
    {
        return $user->hasPermissionTo('view attachments');
    }

    /**
     * Determine whether the attachment can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create attachments');
    }

    /**
     * Determine whether the attachment can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attachment  $model
     * @return mixed
     */
    public function update(User $user, Attachment $model)
    {
        return $user->hasPermissionTo('update attachments');
    }

    /**
     * Determine whether the attachment can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attachment  $model
     * @return mixed
     */
    public function delete(User $user, Attachment $model)
    {
        return $user->hasPermissionTo('delete attachments');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attachment  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete attachments');
    }

    /**
     * Determine whether the attachment can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attachment  $model
     * @return mixed
     */
    public function restore(User $user, Attachment $model)
    {
        return false;
    }

    /**
     * Determine whether the attachment can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Attachment  $model
     * @return mixed
     */
    public function forceDelete(User $user, Attachment $model)
    {
        return false;
    }
}
