<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TasksComments extends Model
{
    /** @use HasFactory<\Database\Factories\TasksCommentsFactory> */
    use HasFactory;

    protected $fillable=['comment','task_id','user_id'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function belongsToManager()
    {
        return $this->task->manager===$this->user;//if true comment was written by manager
    }
}
