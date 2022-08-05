<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'invite_token',
        'user_id'
    ];
}
