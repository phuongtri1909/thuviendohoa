<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlogSidebarSetting;

class BlogSidebarSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlogSidebarSetting::create([
            'section_title' => 'CẬP NHẬT XU HƯỚNG THIẾT KẾ',
            'category_id' => null,
        ]);
    }
}
