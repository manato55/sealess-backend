<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
    ];

    public function sections()
    {
        return $this->hasMany('App\Models\Section');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
}
