@props([''])

<div class="">
    <h5 class="fw-semibold">Bài liên quan</h5>
    <div class="blog-related-list mt-3">
        <div class="row blog-related-item">
            <div class="col-12 col-sm-5">
                <img class="blog-related-img rounded-3 img-fluid" src="{{ asset('images/d/dev/blogs/related1.png') }}"
                    alt="">
            </div>
            <div class="col-12 col-sm-7">
                <div class="blog-related-content py-2">
                    <h6 class="blog-related-title fw-semibold mt-2 mt-md-0">
                        Thiết kế đồ họa và thiết kế web có xu hướng sử dụng hiệu ứng màu đơn sắc cho thiết kế
                    </h6>
                    <div class="blog-related-meta my-2">
                        <span>
                            <img src="{{ asset('images/svg/blogs/time.svg') }}" alt="">
                            <span id="blogDate">02/10/2025</span>
                        </span>
                        <span>
                            <img src="{{ asset('images/svg/blogs/view.svg') }}" alt="">
                            <span id="blogViews">232</span>
                        </span>
                    </div>
                    <p class="blog-related-desc text-md mb-0">
                        Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không “giảm nhiệt” trong
                        năm nay
                        Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không “giảm nhiệt” trong
                        năm nay
                    </p>
                </div>
            </div>
        </div>
    
        <div class="row blog-related-item">
            <div class="col-12 col-sm-5">
                <img class="blog-related-img rounded-3 img-fluid" src="{{ asset('images/d/dev/blogs/related2.png') }}"
                    alt="">
            </div>
            <div class="col-12 col-sm-7">
                <div class="blog-related-content py-2">
                    <h6 class="blog-related-title fw-semibold mt-2 mt-md-0">
                        Thiết kế đồ họa và thiết kế web có xu hướng sử dụng hiệu ứng màu đơn sắc cho thiết kế
                    </h6>
                    <div class="blog-related-meta my-2">
                        <span>
                            <img src="{{ asset('images/svg/blogs/time.svg') }}" alt="">
                            <span id="blogDate">02/10/2025</span>
                        </span>
                        <span>
                            <img src="{{ asset('images/svg/blogs/view.svg') }}" alt="">
                            <span id="blogViews">232</span>
                        </span>
                    </div>
                    <p class="blog-related-desc text-md mb-0">
                        Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không “giảm nhiệt” trong
                        năm nay
                        Xu hướng 3D đã đạt đến đỉnh điểm vào năm 2019 và chắc chắn xu hướng này sẽ không “giảm nhiệt” trong
                        năm nay
                    </p>
                </div>
            </div>
        </div>
    </div>


</div>

@push('styles')
    <style>
        .blog-related-list{
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .blog-related-item {
            align-items: stretch;
        }

        .blog-related-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
    </style>
@endpush

@push('scripts')
@endpush
