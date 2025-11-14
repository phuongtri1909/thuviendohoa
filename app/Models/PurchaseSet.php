<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PurchaseSet extends Model
{
    protected $table = 'purchase_sets';
    protected $fillable = ['user_id', 'set_id', 'coins', 'downloaded_at', 'payment_method'];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function markAsDownloaded()
    {
        $this->downloaded_at = Carbon::now();
        $this->save();
    }
}
