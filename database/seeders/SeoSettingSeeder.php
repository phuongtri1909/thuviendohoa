<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeoSetting;

class SeoSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seoSettings = [
            [
                'page_key' => 'home',
                'title' => 'Trang chủ - ' . config('app.name'),
                'description' => 'Thư viện đồ họa miễn phí và chất lượng cao. Tải về hàng ngàn mẫu thiết kế, mockup, template, vector, PSD file cho Photoshop, Illustrator, CorelDraw và nhiều phần mềm khác.',
                'keywords' => 'thu vien do hoa, mockup, template, vector, PSD, AI, CDR, photoshop, illustrator, coreldraw, thiet ke do hoa, tai mien phi',
                'is_active' => true
            ],
            [
                'page_key' => 'search',
                'title' => 'Tìm kiếm - ' . config('app.name'),
                'description' => 'Tìm kiếm mẫu thiết kế đồ họa theo danh mục, album, thẻ, màu sắc và phần mềm. Hàng ngàn tài nguyên miễn phí và premium.',
                'keywords' => 'tim kiem, search, mockup, template, vector, thiet ke, do hoa',
                'is_active' => true
            ],
            [
                'page_key' => 'albums',
                'title' => 'Albums - ' . config('app.name'),
                'description' => 'Khám phá các bộ sưu tập đồ họa được tuyển chọn. Albums chủ đề đa dạng, cập nhật thường xuyên.',
                'keywords' => 'albums, bo suu tap, chu de, collection, mockup, template',
                'is_active' => true
            ],
            [
                'page_key' => 'blog',
                'title' => 'Blog - ' . config('app.name'),
                'description' => 'Tin tức, hướng dẫn thiết kế đồ họa, tips & tricks, xu hướng thiết kế mới nhất. Cập nhật kiến thức cho designer.',
                'keywords' => 'blog, tin tuc, huong dan, tips, tricks, thiet ke do hoa, xu huong',
                'is_active' => true
            ],
            [
                'page_key' => 'get-link',
                'title' => 'Get Link - ' . config('app.name'),
                'description' => 'Lấy link tải file từ Google Drive nhanh chóng, tiện lợi và miễn phí.',
                'keywords' => 'get link, google drive, tai file, download, link truc tiep',
                'is_active' => true
            ],
        ];

        foreach ($seoSettings as $setting) {
            SeoSetting::updateOrCreate(
                ['page_key' => $setting['page_key']],
                $setting
            );
        }
    }
}
