<?php

namespace App\Policies;

//use Illuminate\Auth\Access\Response;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */

   /* public function viewAny(User $user): bool
    {
        return false;
    }
    */
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->isAdministrator()? true:
         (($task->manager_user_id===$user->id)? true:
         (($task->users->contains($user->id))? true: false));

    }

    /**
     * Determine whether the user can create models.
     */
    /*public function create123_123(User $user): bool 
    {
        return $user->isNormalUser()===true;
    }*/

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return  ($user->isNormalUser()===true) && ($task->manager_user_id=$user->id);
    }

    public function updateStatus(User $user, Task $task): bool
    {
        return $user->isAdministrator()? false:
         (($task->manager_user_id===$user->id)? true:
         (($task->users->contains($user->id))? true: false));

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->isAdministrator()? true:
         (($task->manager_user_id===$user->id)? true: false);
    }

    /**
     * Determine whether the user can restore the model.
     */
    /*
    public function restore(User $user, Task $task): bool
    {
        return false;
    }*/

    /**
     * Determine whether the user can permanently delete the model.
     */
    /*
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }*/
}
