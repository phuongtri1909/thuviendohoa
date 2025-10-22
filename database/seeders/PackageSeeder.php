<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'GÓI ĐỒNG - 199k',
                'plan' => 'bronze',
                'amount' => 199000,
                'coins' => 300,
                'bonus_coins' => 5,
                'expiry' => 90, // 3 tháng
            ],
            [
                'name' => 'GÓI BẠC - 499k',
                'plan' => 'silver',
                'amount' => 499000,
                'coins' => 800,
                'bonus_coins' => 10,
                'expiry' => 180, // 6 tháng
            ],
            [
                'name' => 'GÓI VÀNG - 999k',
                'plan' => 'gold',
                'amount' => 999000,
                'coins' => 1800,
                'bonus_coins' => 15,
                'expiry' => 270, // 9 tháng
            ],
            [
                'name' => 'GÓI BẠCH KIM - 1499k',
                'plan' => 'platinum',
                'amount' => 1499000,
                'coins' => 3000,
                'bonus_coins' => 20,
                'expiry' => 365, // 12 tháng
            ],
        ];

        foreach ($packages as $packageData) {
            Package::updateOrCreate(
                ['plan' => $packageData['plan']],
                $packageData
            );
        }
    }
}