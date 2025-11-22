<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutContent;

class AboutContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            [
                'key' => 'blog-item',
                'title' => null,
                'content' => 'Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không "giảm nhiệt" trong năm nay. Các công nghệ hiện đại và các phần mềm mang lại nhiều cơ hội cho xu hướng 3D phát triển, chúng ta sẽ tiếp tục thấy nhiều tác phẩm thiết kế đồ họa 3D tuyệt vời hơn vào năm 2020. Để tăng sự sáng tạo, các nhà thiết kế thường kết hợp chúng với các yếu tố khác, chẳng hạn như hình ảnh và các yếu tố 2D. Số lượt tìm kiếm "quần áo giá rẻ" bắt đầu giảm mạnh, trong khi cùng thời điểm này, số lượt tìm kiếm "quần áo bền vững" tăng mạnh. Trong cuốn Thế giới không rác thải, tác giả Ron Gonen cho rằng sự chú ý vào xu hướng phát triển bền vững trong ngành thời trang đang tăng đột phá.',
            ],
            [
                'key' => 'get-link',
                'title' => 'HƯỚNG DẪN TẢI FILE:',
                'content' => 'Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không "giảm nhiệt" trong năm nay. Các công nghệ hiện đại và các phần mềm mang lại nhiều cơ hội cho xu hướng 3D phát triển, chúng ta sẽ tiếp tục thấy nhiều tác phẩm thiết kế đồ họa 3D tuyệt vời hơn vào năm 2020. Để tăng sự sáng tạo, các nhà thiết kế thường kết hợp chúng với các yếu tố khác, chẳng hạn như hình ảnh và các yếu tố 2D. Số lượt tìm kiếm "quần áo giá rẻ" bắt đầu giảm mạnh, trong khi cùng thời điểm này, số lượt tìm kiếm "quần áo bền vững" tăng mạnh. Trong cuốn Thế giới không rác thải, tác giả Ron Gonen cho rằng sự chú ý vào xu hướng phát triển bền vững trong ngành thời trang đang tăng đột phá. Trong cuốn Thế giới không rác thải, tác giả Ron Gonen cho rằng sự chú ý vào xu hướng phát triển bền vững trong ngành thời trang đang tăng đột phá',
            ],
        ];

        foreach ($contents as $content) {
            AboutContent::updateOrCreate(
                ['key' => $content['key']],
                $content
            );
        }
    }
}
