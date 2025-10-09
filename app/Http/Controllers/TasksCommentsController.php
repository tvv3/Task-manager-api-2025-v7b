<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\TasksComments;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTasksCommentsRequest;
use App\Http\Requests\UpdateTasksCommentsRequest;


use App\Http\Resources\TasksCommentsCollection;
use App\Http\Resources\TasksCommentsResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;



class TasksCommentsController extends Controller
{
    
     
    public function list_comments_for_task(Task $task)
    {
          Gate::authorize('listComments',[TasksComments::class, $task]);
        
          $comments= TasksComments::where('task_id',$task->id)
          ->with(['user'])->orderBy('created_at', 'desc');
       if ($comments->count()>=1) 
          {return $comments->paginate(2);}
       //else
         return response(['message'=>'No comments for this task'], 404);
        
    }

    
    public function store(StoreTasksCommentsRequest $request)
    {
       
        $validated=$request->validated();// the validated fields
        //first check if tasks exist through validation then check authorization gate
        Gate::authorize('createComment',[TasksComments::class, Task::find($request->task_id)]);


        $validated['user_id']=Auth::user()->id;
        $taskComment=TasksComments::create($validated);//create
        return new TasksCommentsResource($taskComment);
    }

    /**
     * Display the specified resource.
     */

    public function show(TasksComments $tasksComments)
    {
        Gate::authorize('viewComment');
        return new TasksCommentsResource($tasksComments);
    }

    
    public function update(UpdateTasksCommentsRequest $request, TasksComments $tasksComment)
    {
        Gate::authorize('updateComment',[TasksComments::class,  $tasksComment]);
       //can be put before validation cause we have the comment id as param
        $validated=$request->validated();// the validated fields
        if ($request->task_id!=$tasksComment->task_id)
        {return response(['errors'=>['comment'=>['Update error! This comment belongs to another task']]], 500);}
        $tasksComment->update(["comment"=>$validated['comment']]);//update
        
        return new TasksCommentsResource($tasksComment);//data.data.comment
    }

    
    public function destroy(TasksComments $tasksComment)
    {
       Gate::authorize('deleteComment',[TasksComments::class, $tasksComment]);
        $deleted=$tasksComment->delete();
         if ($deleted>0) 
             return response(['message'=>'Comment deleted'], 200);
        //else
        return response(['message'=>'Comment not deleted'], 500);
       
        //return response()->noContent();
    }
}
