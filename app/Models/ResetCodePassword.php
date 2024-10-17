<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetCodePassword extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'email',
        'code',
        'code_type',
        'sent_recipt',
        'password_changed',
        'created_at',
    ];
}
