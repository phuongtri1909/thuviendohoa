<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GetLinkHistory extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'title',
        'favicon',
        'coins_spent',
    ];

    protected $casts = [
        'coins_spent' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
