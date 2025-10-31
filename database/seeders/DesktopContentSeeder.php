<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DesktopContent;

class DesktopContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DesktopContent::updateOrCreate(
            ['key' => DesktopContent::KEY_DESKTOP],
            [
                'name' => 'Desktop Content',
                'logo' => 'images/d/desktops/logo.png',
                'title' => 'CHỌN GÓI TÀI KHOẢN VIP ĐỂ TẢI FILE',
                'description' => 'Bạn thân mến! Việc <span class="fw-bold">đăng kí VIP</span>, bạn sẽ nhận được các gói XU tương ứng và kích hoạt quyền tải không giới hạn, đồng thời nhận được hỗ trợ chỉnh sửa file từ đội ngũ Hidesign. Với nền tảng chia sẻ file thiết kế, Hidesign liên tục cải tiến nhằm mang đến cho bạn trải nghiệm tốt hơn với các ưu thế:',
                'features' => [
                    [
                        'icon' => 'images/svg/desktops/big-data.svg',
                        'title' => 'Kho dữ liệu lớn',
                        'description' => 'Hơn 5.000GB file tại kho đồ họa',
                    ],
                    [
                        'icon' => 'images/svg/desktops/update.svg',
                        'title' => 'Luôn cập nhật mới',
                        'description' => 'Cập nhật hàng ngàn file mới mỗi ngày',
                    ],
                    [
                        'icon' => 'images/svg/desktops/dadang.svg',
                        'title' => 'Đa dang sản phẩm',
                        'description' => 'Thiết kế đa dang sản phẩm và chủ đề',
                    ],
                    [
                        'icon' => 'images/svg/desktops/high.svg',
                        'title' => 'Chất lượng cao',
                        'description' => 'Sở hữu những file chất lượng cao',
                    ],
                ],
                'status' => true,
            ]
        );
    }
}




