<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GetLinkConfig extends Model
{
    protected $fillable = [
        'coins',
    ];

    protected $casts = [
        'coins' => 'integer',
    ];

    public static function getInstance()
    {
        return self::firstOrCreate(
            ['id' => 1],
            ['coins' => 5]
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $existingRecords = self::where('id', '!=', $model->id)->get();
            foreach ($existingRecords as $record) {
                $record->delete();
            }
        });

        static::updating(function ($model) {
            $otherRecords = self::where('id', '!=', $model->id)->get();
            foreach ($otherRecords as $record) {
                $record->delete();
            }
        });
    }
}
