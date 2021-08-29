<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class EmailVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'expired_at',
        'name',
        'department_id',
        'section_id',
        'job_title_id',
    ];

    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }
}
