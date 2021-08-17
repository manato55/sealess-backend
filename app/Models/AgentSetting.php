<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'agent_user_id',
        'is_enabled',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User');
    }

    public function agent_user()
    {
        return $this->belongsTo('App\Models\User','agent_user_id', 'id');
    }
}
