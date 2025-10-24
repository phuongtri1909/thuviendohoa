<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'name' => 'Ngân Hàng Phương Đông',
                'code' => 'OCB',
                'logo' => '',
                'account_number' => 'CASS133326',
                'account_name' => 'PHAM VIET THANG',
            ],
        ];

        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
