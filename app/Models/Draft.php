<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Askedio\SoftCascade\Traits\SoftCascadeTrait;


class Draft extends Model
{
    use HasFactory, SoftDeletes, SoftCascadeTrait;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'filename',
        'route1',
        'route2',
        'route3',
        'route4',
        'route5',
        'approved',
        'intercepted',
        'process',
        'is_agent',
    ];

    protected $softCascade = [
        'returnedTask',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function route1User()
    {
        return $this->belongsTo('App\Models\User', 'route1', 'id')->withTrashed();
    }

    public function route2User()
    {
        return $this->belongsTo('App\Models\User', 'route2', 'id')->withTrashed();
    }

    public function route3User()
    {
        return $this->belongsTo('App\Models\User', 'route3', 'id')->withTrashed();
    }

    public function route4User()
    {
        return $this->belongsTo('App\Models\User', 'route4', 'id')->withTrashed();
    }

    public function route5User()
    {
        return $this->belongsTo('App\Models\User', 'route5', 'id')->withTrashed();
    }

    public function returnedTask()
    {
        return $this->hasOne('App\Models\ReturnedTask');
    }

    public function route1Agent()
    {
        return $this->hasMany('App\Models\User', 'route1', 'user_id');
    }

    public function route2Agent()
    {
        return $this->hasMany('App\Models\User', 'route2', 'user_id');
    }

    public function route3Agent()
    {
        return $this->hasMany('App\Models\User', 'route3', 'user_id');
    }

    public function route4Agent()
    {
        return $this->hasMany('App\Models\User', 'route4', 'user_id');
    }

    public function route5Agent()
    {
        return $this->hasMany('App\Models\User', 'route5', 'user_id');
    }

    public function agent_statuses()
    {
        return $this->hasMany('App\Models\AgentStatus');
    }
}
