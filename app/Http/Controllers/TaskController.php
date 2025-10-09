<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\TasksComments;
use App\Models\TasksUsers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
//use Illuminate\Auth\Access\Response;
//use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $ok_response=request()->user()->isAdministrator();
        
        if ($ok_response)
        {
        //return 'admin';
         $tasks2=Task::with(['manager','users'])->paginate(3);
         if (Task::with(['manager','users'])->count()>0)
            return $tasks2;
         else 
            return response(['message'=>'No tasks found!'], 404);
        
        }
        else
        {
            //return 'user';
            $auth_user_id=Auth::user()->id;
            /*return DB::table('tasks')->where('manager_user_id','=',$auth_user_id)
            ->orWhereExists(TasksUsers::where('task_id','=','tasks.id')->where('user_id','=',$auth_user_id))
           ->toSql();*/
            $tasks=Task::
            whereExists(TasksUsers::where('tasks_users.task_id','=','tasks.id')->where('tasks_users.user_id','=',$auth_user_id))
            ->orWhere('tasks.manager_user_id','=',$auth_user_id)
            ->with(['manager','users']);
            //->toRawSql();
            //return ['message'=>$tasks];
            if ($tasks->count()>0)
            return $tasks->paginate(2);
            else 
            return response(['message'=>'No tasks found for this user!'], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    //any user with role=user can do store 
    public function store(StoreTaskRequest $request)
    {
        Gate::allowIf(fn(User $user) => $user->isNormalUser(), 'You are not authorized to create a task!', 403);
        
        $validated=$request->validated();// the validated fields
        
        $validated['manager_user_id']=Auth::user()->id;
        $task=Task::create($validated);//create
        return new TaskResource($task);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        Gate::authorize('view', $task);
        $task2=Task::where('id','=',$task->id)->with(['manager','users'])->get();
        return new TaskResource($task2);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);
        $validated=$request->validated();// the validated fields
       // $validated['manager_task_id']=Auth::user()->id;
        $task->update($validated);// update
        return new TaskResource($task);
    }

    public function updateStatus(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('updateStatus', $task);
        $validated=$request->validated();// the validated fields
       // $validated['manager_task_id']=Auth::user()->id;
        $task->update(['is_done'=>$validated['is_done']]);// update
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete',$task);
        $comments=TasksComments::where('task_id','=',$task->id)->get();
        $team=TasksUsers::where('task_id','=',$task->id)->get();
         if (count($comments)>=1)
            return response(['message'=>'Task not deleted. Comments found.'], 403);
         else if (count($team)>=1)
            return response(['message'=>'Task not deleted. Team members found.'], 403);
         else
         {
        $deleted=$task->delete();
        if ($deleted>0)
            return response(['message'=>'Task deleted'],200);
        //else
            return response(['message'=>'Task not deleted'],500);
           
         }
        //return response()->noContent();
        
    }

    }
