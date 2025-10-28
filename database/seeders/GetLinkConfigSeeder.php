<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GetLinkConfig;

class GetLinkConfigSeeder extends Seeder
{
    public function run(): void
    {
        GetLinkConfig::firstOrCreate(
            ['id' => 1],
            ['coins' => 5]
        );
    }
}
