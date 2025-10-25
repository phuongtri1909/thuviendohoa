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

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getPlanColor()
    {
        return match($this->plan) {
            self::PLAN_BRONZE => 'color-bronze',
            self::PLAN_SILVER => 'color-silver',
            self::PLAN_GOLD => 'color-gold',
            self::PLAN_PLATINUM => 'color-platinum',
        };
    }

    public function getPlanFilter()
    {
        return match($this->plan) {
            self::PLAN_BRONZE => 'filter-bronze',
            self::PLAN_SILVER => 'filter-silver',
            self::PLAN_GOLD => 'filter-gold',
            self::PLAN_PLATINUM => 'filter-platinum',
        };
    }

    public function getPlanGradient()
    {
        return match($this->plan) {
            self::PLAN_BRONZE => 'bg-gradient-bronze',
            self::PLAN_SILVER => 'bg-gradient-silver',
            self::PLAN_GOLD => 'bg-gradient-gold',
            self::PLAN_PLATINUM => 'bg-gradient-platinum',
        };
    }

    public function getPlanName()
    {
        return match($this->plan) {
            self::PLAN_BRONZE => 'TK ĐỒNG',
            self::PLAN_SILVER => 'TK BẠC',
            self::PLAN_GOLD => 'TK VÀNG',
            self::PLAN_PLATINUM => 'TK BẠCH KIM',
        };
    }

    public function getPlanPluralName()
    {
        return match($this->plan) {
            self::PLAN_BRONZE => 'ĐỒNG',
            self::PLAN_SILVER => 'BẠC',
            self::PLAN_GOLD => 'VÀNG',
            self::PLAN_PLATINUM => 'BẠCH KIM',
        };
    }
}
