<div class="blog-sidebar">
    <div>
        <h4 class="fw-semibold title-tab">DANH MỤC BLOG</h4>
        <div class="mt-2 category-blog">
            @foreach($categories as $category)
                <span class="color-primary-12 category-blog-item">
                    <img class="me-2" src="{{ asset('images/svg/blogs/arrow-right.svg') }}" alt="">
                    {{ $category->name }} ({{ $category->blogs->count() }})
                </span>
            @endforeach

            @if($sidebarSetting->extra_link_title && $sidebarSetting->extra_link_url)
                <a href="{{ $sidebarSetting->extra_link_url }}" class="color-primary-12 fw-semibold text-decoration category-blog-item">
                    <img class="me-2" src="{{ asset('images/svg/blogs/arrow-right.svg') }}" alt="">
                    {{ $sidebarSetting->extra_link_title }}
                </a>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <h6 class="color-primary-12">{{ $sidebarSetting->section_title ?? 'CẬP NHẬT XU HƯỚNG THIẾT KẾ' }}</h6>
        <h4 class="fw-semibold title-tab">{{ $sidebarSetting->section_subtitle ?? 'CÂU CHUYỆN ĐỒ HỌA' }}</h4>
        <div class="mt-3">
            @forelse($sidebarBlogs as $sidebarBlog)
                <a href="{{ route('blog.item', $sidebarBlog->slug) }}" class="design-item text-decoration-none">
                    <img class="img-design" 
                         src="{{ $sidebarBlog->image ? asset('storage/' . $sidebarBlog->image) : asset('/images/d/dev/blogs/design1.jpg') }}" 
                         alt="{{ $sidebarBlog->title }}">
                    <div>
                        <h6 class="fw-semibold color-primary-12">{{ Str::limit($sidebarBlog->title, 200) }}</h6>
                        <span class="color-primary-12">
                            <img src="{{ asset('images/svg/blogs/time.svg') }}" alt="">
                            <span>{{ $sidebarBlog->created_at->format('d/m/Y') }}</span>
                        </span>
                    </div>
                </a>
            @empty
                <div class="design-item">
                    <img class="img-design" src="{{ asset('/images/d/dev/blogs/design1.jpg') }}" alt="design1">
                    <div>
                        <h6 class="fw-semibold">Chưa có bài viết nào</h6>
                        <span>
                            <img src="{{ asset('images/svg/blogs/time.svg') }}" alt="">
                            <span>--/--/----</span>
                        </span>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
