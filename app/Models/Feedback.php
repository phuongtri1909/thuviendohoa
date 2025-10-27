<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'message',
        'ip_address',
        'user_agent',
        'status',
        'admin_reply',
        'admin_id',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_READ = 'read';
    const STATUS_REPLIED = 'replied';

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Accessor for status label
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_READ => 'Đã đọc',
            self::STATUS_REPLIED => 'Đã phản hồi',
            default => ucfirst($this->status),
        };
    }

    // Accessor for status color
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_READ => 'info',
            self::STATUS_REPLIED => 'success',
            default => 'secondary',
        };
    }

    // Scope for pending feedback
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // Method to mark as read
    public function markAsRead()
    {
        $this->update(['status' => self::STATUS_READ]);
    }

    // Method to reply
    public function reply($adminId, $reply)
    {
        $this->update([
            'status' => self::STATUS_REPLIED,
            'admin_reply' => $reply,
            'admin_id' => $adminId,
            'replied_at' => now(),
        ]);
    }
}
