<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogNowpayment extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    protected $fillable = [
        'post',
        'json',
        'mark',
    ];
}
