<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomEncryptable;

class TwitterSetting extends Model
{
    use CustomEncryptable;

    protected $table = 'twitter_settings';

    protected $fillable = [
        'twitter_client_id',
        'twitter_client_secret',
        'twitter_redirect',
    ];

    protected $encryptable = [
        'twitter_client_secret',
    ];
}