<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnedTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'draft_id',
        'comment',
    ];

    public function draft()
    {
        return $this->belongsTo('App\Models\Draft');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
