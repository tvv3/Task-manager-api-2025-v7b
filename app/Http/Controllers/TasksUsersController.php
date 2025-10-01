<?php

namespace App\Http\Controllers;

use App\Models\TasksUsers;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTasksUsersRequest;


use App\Http\Resources\TasksUsersCollection;
use App\Http\Resources\TasksUsersResource;
use App\Models\Task;
use App\Models\User;
use App\Models\UsersRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TasksUsersController extends Controller
{
    
    public function store(StoreTasksUsersRequest $request)
    {
        $fields=$request->validated();

        $task=Task::find($fields['task_id']);

        Gate::allowIf(function (User $user) use ($task) { return $user->id===$task->manager_user_id;},
         'You are not the manager of this task', 403);

        $user=UsersRole::where('user_id','=',$request->user_id)
                       ->where('role','=','user')->first();

        if (!isset($user))
        {
            return response("You must set a normal user as part of the task's team", 403);
        }
        // else test unicitate member in echipa

         $exists = TasksUsers::where('task_id', $fields['task_id'])
                      ->where('user_id', $fields['user_id'])
                      ->exists();

         if ($exists) {
            return response()->json(['message' => 'User already in team'], 409);
         }

        //else
        $tasksUsers=new TasksUsers();
        $tasksUsers->task_id=$fields['task_id'];
        $tasksUsers->user_id=$fields['user_id'];
        //$tasksUsers->save();
        try {
            $tasksUsers->save();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) { // SQLSTATE integrity constraint
                return response()->json(['message' => 'User already in team'], 409);
            }
        
            throw $e; // pentru alte erori
        }
        return new TasksUsersResource($tasksUsers);
    }

     
    public function destroy2(Task $task, User $user)
    {
        
        Gate::allowIf(function (User $user) use ($task) { return $user->id===$task->manager_user_id||$user->isAdministrator();},
         'You are not the manager of this task nor an administrator', 403);

       
        $deleted = DB::delete('delete from tasks_users where task_id='.$task->id.' and user_id='.$user->id);
        if ($deleted!=0) return response()->json(['message'=>'User unassociated to task'], 200);
        
        return response()->json(['message'=>'Team member not identified'], 404);
    }
/*
    public function search_users_for_task(Task $task)
    {
        Gate::allowIf(function (User $user) use ($task) { $ok= $user->id===$task->manager_user_id||$user->isAdministrator();
            if (!$ok)
            {
                $ok=TasksUsers::where('task_id','=',$task->id)
                       ->where('user_id','=',$user->id)
                       ->count == 1 ? true:false;
            }
            return $ok;
        },
         "You are not the manager of this task nor an administrator nor a memeber of the task's team", 403);

        return new TasksUsersCollection(TasksUsers::where('task_id','=',$task->id)->get());
    }
*/
    public function other_potential_users_for_task(Task $task)
    {
        Gate::allowIf(function (User $user) use ($task) { $ok= $user->id===$task->manager_user_id;
            return $ok;
        },
         "You are not the manager of this task", 403);
        
        $taskUsersCond=DB::table('tasks_users')
        ->where('task_id','=',$task->id)
        ->select(['user_id']);
        //if (!isset($taskUsersCond)) 
        //{$taskUsersCond=[$task->manager_user_id];}
        
        $users=[];
        $users=DB::table('users')->join('users_roles','users.id','=','users_roles.user_id')
            ->where('users_roles.role','=',"user")
            ->where('users.id','<>',$task->manager_user_id)
            ->whereNotIn('users.id', $taskUsersCond)
            ->select(['users.id','users.name'])
            ->get();

       return $users;
    }

}
