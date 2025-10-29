<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FooterSetting;

class FooterSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FooterSetting::create([
            'facebook_url' => 'https://www.facebook.com/thuvien24hh',
            'support_hotline' => '0944 133 994',
            'support_email' => 'printon.hcm@gmail.com',
            'support_fanpage' => 'Printon',
            'partners' => [],
        ]);
    }
}
