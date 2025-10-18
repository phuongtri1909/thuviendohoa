<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomEncryptable;

class FacebookSetting extends Model
{
    use CustomEncryptable;

    protected $table = 'facebook_settings';

    protected $fillable = [
        'facebook_client_id',
        'facebook_client_secret',
        'facebook_redirect',
    ];

    protected $encryptable = [
        'facebook_client_secret',
    ];
}