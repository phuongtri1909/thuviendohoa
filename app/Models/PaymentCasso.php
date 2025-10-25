<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Bank;

class PaymentCasso extends Model
{
    protected $table = 'payment_cassos';
    protected $fillable = [
        'user_id',
        'bank_id',
        'transaction_code',
        'package_plan',
        'coins',
        'amount',
        'expiry',
        'status',
        'note',
        'processed_at',
        'casso_response',
        'casso_transaction_id',     
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
