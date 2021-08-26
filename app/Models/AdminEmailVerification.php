<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEmailVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'expired_at',
    ];


    public function company()
    {
        return $this->hasOne('App\Models\Company', 'email', 'email');
    }
}
