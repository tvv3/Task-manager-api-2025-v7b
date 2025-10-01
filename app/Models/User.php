<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable; //HasApiTokens - not used anymore

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userRole(): HasOne
    {
        return $this->hasOne(UsersRole::class);
    }

    public function tasksAsManager(): HasMany
    {
        return $this->hasMany(Task::class,'manager_user_id');
    }

    /*
    public function tasksAsUser(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class,TasksUsers::class,'manager_user_id');
    }*/

    public function tasksAsUser(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'tasks_users',
                     'user_id',            // foreign key pe modelul curent (User)
                     'task_id'             // foreign key pe modelul țintă (Task)
    );
                 // ->using(TasksUsers::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TasksComments::class,'user_id');
    }

    public function isAdministrator()
    {
        return $this->userRole->role==='admin';
    }

    public function isNormalUser()
    {
        return $this->userRole->role==='user';
    }
}
