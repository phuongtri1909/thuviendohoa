<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinTransaction extends Model
{
    protected $table = 'coin_transactions';
    
    protected $fillable = [
        'user_id',
        'admin_id',
        'amount',
        'type',
        'reason',
        'note',
        'target_data'
    ];

    protected $casts = [
        'target_data' => 'array',
        'amount' => 'integer'
    ];

    const TYPE_MANUAL = 'manual';
    const TYPE_PACKAGE_BONUS = 'package_bonus';
    const TYPE_REFUND = 'refund';
    const TYPE_PENALTY = 'penalty';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}