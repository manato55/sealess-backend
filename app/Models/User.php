<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use \Askedio\SoftCascade\Traits\SoftCascadeTrait;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, SoftCascadeTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'section',
        'job_title',
        'user_type',
        'company_id',
        'department_id',
        'section_id',
        'job_title_id',
    ];

    protected $softCascade = [
        'drafts',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function drafts()
    {
        return $this->hasMany('App\Models\Draft');
    }

    public function agentSetting()
    {
        return $this->hasOne('App\Models\AgentSetting');
    }

    public function passwordReset()
    {
        return $this->hasOne('App\Models\PasswordReset','email', 'email');
    }

    public function routes()
    {
        return $this->hasMany('App\Models\Route');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function section()
    {
        return $this->belongsTo('App\Models\Section');
    }

    public function jobTitle()
    {
        return $this->belongsTo('App\Models\JobTitle');
    }
}
