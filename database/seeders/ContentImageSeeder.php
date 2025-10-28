<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentImage;

class ContentImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Content 1 - với button overlay
        ContentImage::updateOrCreate(
            ['key' => ContentImage::KEY_CONTENT1],
            [
                'name' => 'Content 1 (Có button)',
                'image' => 'images/d/contents/content1.png',
                'url' => null,
                'button_text' => '> Xem thêm',
                'button_position_x' => '31%',
                'button_position_y' => '80%',
                'status' => true,
            ]
        );

        // Content 2 - chỉ có hình
        ContentImage::updateOrCreate(
            ['key' => ContentImage::KEY_CONTENT2],
            [
                'name' => 'Content 2 (Chỉ hình)',
                'image' => 'images/d/contents/content2.png',
                'url' => null,
                'button_text' => null,
                'button_position_x' => null,
                'button_position_y' => null,
                'status' => true,
            ]
        );
    }
}

