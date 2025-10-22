<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'plan',
        'amount',
        'coins',
        'bonus_coins',
        'expiry',
    ];

    const PLAN_BRONZE = 'bronze';
    const PLAN_SILVER = 'silver';
    const PLAN_GOLD = 'gold';
    const PLAN_PLATINUM = 'platinum';
}
