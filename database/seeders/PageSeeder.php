<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Giới thiệu về Printon',
                'slug' => 'gioi-thieu-printon',
                'content' => '<h2>Giới thiệu về Printon</h2>
                <p>Printon là nền tảng cung cấp các mẫu thiết kế đồ họa chuyên nghiệp, phục vụ cho nhu cầu thiết kế của cá nhân và doanh nghiệp.</p>
                <p>Với hàng nghìn mẫu thiết kế đa dạng, chúng tôi cam kết mang đến cho bạn những sản phẩm chất lượng cao nhất.</p>',
                'status' => 1,
                'order' => 1,
            ],
            [
                'title' => 'Điều khoản chung',
                'slug' => 'dieu-khoan-chung',
                'content' => '<h2>Điều khoản sử dụng</h2>
                <p>Khi sử dụng dịch vụ của Printon, bạn đồng ý tuân thủ các điều khoản sau:</p>
                <ul>
                    <li>Không sao chép, phân phối lại các mẫu thiết kế khi chưa được phép</li>
                    <li>Sử dụng mẫu thiết kế đúng mục đích và tuân thủ bản quyền</li>
                    <li>Không vi phạm các quy định pháp luật hiện hành</li>
                </ul>',
                'status' => 1,
                'order' => 2,
            ],
            [
                'title' => 'Chính sách bảo mật',
                'slug' => 'chinh-sach-bao-mat',
                'content' => '<h2>Chính sách bảo mật thông tin</h2>
                <p>Printon cam kết bảo mật thông tin cá nhân của khách hàng:</p>
                <ul>
                    <li>Thông tin cá nhân được mã hóa và bảo vệ an toàn</li>
                    <li>Không chia sẻ thông tin khách hàng cho bên thứ ba</li>
                    <li>Sử dụng thông tin chỉ để phục vụ dịch vụ và hỗ trợ khách hàng</li>
                </ul>',
                'status' => 1,
                'order' => 3,
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
