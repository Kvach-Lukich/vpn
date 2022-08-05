<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'invite_token',
    ];
}
