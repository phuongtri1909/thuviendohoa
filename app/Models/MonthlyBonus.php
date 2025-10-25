<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MonthlyBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'month',
        'total_users',
        'total_coins',
        'bonus_per_user',
        'user_ids',
        'notes',
        'processed_at',
    ];

    protected $casts = [
        'user_ids' => 'array',
        'processed_at' => 'datetime',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function getFormattedMonthAttribute()
    {
        return Carbon::createFromFormat('Y-m', $this->month)->format('m/Y');
    }

    public function getTotalCoinsFormattedAttribute()
    {
        return number_format($this->total_coins);
    }

    public function getProcessedAtFormattedAttribute()
    {
        return $this->processed_at->format('d/m/Y H:i');
    }

    
}