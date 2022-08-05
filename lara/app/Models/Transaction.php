<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'check_count',
        'user_id',
        'payment_id',
        'price_amount',
        'pay_address',
        'payin_extra_id',
        'pay_amount',
        'price_currency',
        'actually_paid',
        'pay_currency',
        'order_id',
        'order_description',
        'purchase_id',
        'invoice_id',
        'outcome_amount',
        'outcome_currency',
        'payment_status',
        'status_id',
        'payout_hash',
        'payin_hash',
        'np_created_at'
    ];
}
