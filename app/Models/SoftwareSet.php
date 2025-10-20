<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareSet extends Model
{
    protected $table = 'software_sets';
    protected $fillable = ['software_id', 'set_id'];

    public function software()
    {
        return $this->belongsTo(Software::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }
}
