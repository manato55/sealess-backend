<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'draft_id',
        'original_user',
        'agent_user',
        'route',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'original_user', 'id');
    }
}
