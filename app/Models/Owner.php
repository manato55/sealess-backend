<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Owner extends User
{
    use HasFactory;

    protected $hidden = [
        'password',
    ];
}
