<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\TasksComments;
use App\Models\User;
use App\Models\Task;

class TasksCommentsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function listComments(User $user, Task $task): bool
    {
        return $user->isAdministrator()? true:
        (($task->manager_user_id===$user->id)? true:
        (($task->users->contains($user->id))? true: false));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewComment(User $user, TasksComments $tasksComments): bool
    {
        return $user->isAdministrator()? true:
        (($tasksComments->task->manager_user_id===$user->id)? true:
        (($tasksComments->task->users->contains($user->id))? true: false));
    }

    /**
     * Determine whether the user can create models.
     */
   /* public function createComment(User $user): bool
    {
        return $user->isNormalUser();
    } */

    public function createComment(User $user, Task $task): bool
{
    
    return $user->isNormalUser() && ( $task->manager_user_id === $user->id || $task->users->contains($user->id));
}

    /**
     * Determine whether the user can update the model.
     */
    public function updateComment(User $user, TasksComments $tasksComments): bool
    {
        return  ($user->isNormalUser()===true) && ($tasksComments->user_id===$user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteComment(User $user, TasksComments $tasksComments): bool
    {
        return  ($user->isAdministrator()===true) || (($user->isNormalUser()===true)&&($tasksComments->user_id=$user->id));
 
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TasksComments $tasksComments): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TasksComments $tasksComments): bool
    {
        return false;
    }
}
