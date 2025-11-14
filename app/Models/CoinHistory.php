<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'amount',
        'type',
        'source',
        'reason',
        'description',
        'metadata',
        'is_read',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
    ];

    // Constants for types
    const TYPE_PAYMENT = 'payment';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_MANUAL = 'manual';
    const TYPE_MONTHLY_BONUS = 'monthly_bonus';
    const TYPE_GETLINK = 'getlink';
    const TYPE_FREE_DOWNLOAD = 'free_download';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function getFormattedAmountAttribute()
    {
        $prefix = $this->amount > 0 ? '+' : '';
        return $prefix . number_format($this->amount);
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            self::TYPE_PAYMENT => 'Nạp tiền',
            self::TYPE_PURCHASE => 'Mua file',
            self::TYPE_MANUAL => 'Thủ công',
            self::TYPE_MONTHLY_BONUS => 'Thưởng tháng',
            self::TYPE_GETLINK => 'Get link',
            self::TYPE_FREE_DOWNLOAD => 'Dùng lượt miễn phí',
            default => 'Khác'
        };
    }

    public function getAmountColorAttribute()
    {
        return $this->amount > 0 ? 'text-success' : 'text-danger';
    }
}