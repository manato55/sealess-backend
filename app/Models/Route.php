<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'route1',
        'route2',
        'route3',
        'route4',
        'route5',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function route1User()
    {
        return $this->belongsTo('App\Models\User', 'route1', 'id');
    }

    public function route2User()
    {
        return $this->belongsTo('App\Models\User', 'route2', 'id');
    }

    public function route3User()
    {
        return $this->belongsTo('App\Models\User', 'route3', 'id');
    }

    public function route4User()
    {
        return $this->belongsTo('App\Models\User', 'route4', 'id');
    }

    public function route5User()
    {
        return $this->belongsTo('App\Models\User', 'route5', 'id');
    }


}
