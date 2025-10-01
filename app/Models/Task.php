<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable=['title','is_done','manager_user_id'];

    protected $casts=[
        'is_done' => 'boolean',
    ];

    //protected $hidden =['updated_at',];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tasks_users', 'task_id', 'user_id');
        //->using(TasksUsers::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
        // return $this->belongsTo(Post::class, 'foreign_key', 'owner_key');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TasksComments::class);
    }

}


