<div class="col-12 col-md-6 col-lg-4 col-xl-3">
    <a href="{{ route('blog.item', $blog->slug) }}" class="blog-item text-decoration-none d-inline-block">
        <img class="img-blog-item img-fluid" 
             src="{{ $blog->image ? asset('storage/' . $blog->image) : asset('images/d/dev/blogs/blog1.png') }}" 
             alt="{{ $blog->title }}">
        <div class="blogs-knowledge-item-info text-center py-2 text-sm color-primary-12">
            <span>
                <img src="{{ asset('images/svg/blogs/time.svg') }}" alt="">
                <span>{{ $blog->created_at->format('d/m/Y') }}</span>
            </span>
            <span>
                <img src="{{ asset('images/svg/blogs/view.svg') }}" alt="">
                <span>{{ $blog->views ?? 0 }}</span>
            </span>
        </div>
        <p class="px-4 color-primary-12 fw-semibold text-md mb-2">
            {{ Str::limit($blog->title, 80) }}
        </p>
    </a>
</div>
