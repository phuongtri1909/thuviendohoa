@props(['relatedBlogs'])

<div class="">
    <h5 class="fw-semibold">Bài liên quan</h5>
    
    @if($relatedBlogs && $relatedBlogs->count() > 0)
        <div class="blog-related-list mt-3">
            @foreach($relatedBlogs as $relatedBlog)
                <a href="{{ route('blog.item', $relatedBlog->slug) }}" class="text-decoration-none">
                    <div class="row blog-related-item">
                        <div class="col-12 col-sm-5">
                            <img class="blog-related-img rounded-3 img-fluid" 
                                 src="{{ $relatedBlog->image ? asset('storage/' . $relatedBlog->image) : asset('images/d/dev/blogs/related1.png') }}"
                                 alt="{{ $relatedBlog->title }}">
                        </div>
                        <div class="col-12 col-sm-7">
                            <div class="blog-related-content py-2">
                                <h6 class="blog-related-title fw-semibold mt-2 mt-md-0 color-primary-12">
                                    {{ $relatedBlog->title }}
                                </h6>
                                <div class="blog-related-meta my-2 color-primary-13">
                                    <span>
                                        <img src="{{ asset('images/svg/blogs/time.svg') }}" alt="">
                                        <span>{{ $relatedBlog->created_at->format('d/m/Y') }}</span>
                                    </span>
                                    <span>
                                        <img src="{{ asset('images/svg/blogs/view.svg') }}" alt="">
                                        <span>{{ $relatedBlog->views ?? 0 }}</span>
                                    </span>
                                </div>
                                <p class="blog-related-desc text-md mb-0 color-primary-12">
                                    {{ cleanDescription($relatedBlog->content, 150) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <p class="text-muted mt-3">Chưa có bài viết liên quan.</p>
    @endif
</div>

@push('styles')
    <style>
        .blog-related-list{
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .blog-related-list a {
            transition: transform 0.3s ease;
        }
        
        .blog-related-list a:hover {
            transform: translateX(5px);
        }
        
        .blog-related-item {
            align-items: stretch;
        }

        .blog-related-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.3s ease;
        }
        
        .blog-related-list a:hover .blog-related-img {
            opacity: 0.85;
        }

        .blog-related-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
            max-height: calc(1.4em * 2);
        }

        .blog-related-desc {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.6;
            max-height: calc(1.6em * 3);
        }
        
        .blog-related-meta {
            display: flex;
            gap: 15px;
            font-size: 0.875rem;
        }
        
        .blog-related-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
@endpush

@push('scripts')
@endpush
