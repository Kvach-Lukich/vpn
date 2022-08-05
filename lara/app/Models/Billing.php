<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'blance',
        'active_subscription',
        'url_safe_public_key',
        'wg_json',
    ];
}
