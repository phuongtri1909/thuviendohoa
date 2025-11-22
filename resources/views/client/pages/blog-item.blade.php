@extends('client.layouts.app')
@section('title', 'Blog')
@section('description', 'Blog')
@section('keyword', 'Blog')

@section('content')
    <div class="container-custom blog-container">
        <div class="pt-3">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb color-primary-12">
                    <li class="breadcrumb-item "><a class="color-primary-12 text-decoration-none"
                            href="{{ route('home') }}">TRANG CHỦ</a></li>
                    <li class="breadcrumb-item "><a class="color-primary-12 text-decoration-none"
                            href="{{ route('blog') }}">{{ $blog->category->name }}</a></li>
                    <li class="breadcrumb-item color-primary-12 active fw-semibold" aria-current="page">{{ $blog->title }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12 col-lg-9 mt-5">
                <div class="bg-blog-item ps-2 ps-sm-4 pe-2 pe-sm-5 position-relative">
                    <div class="blog-layout d-flex">
                        <div class="blog-button-wrap">
                            <x-client.button-blog-item />
                        </div>

                        <div class="blog-main flex-grow-1 ps-2 ps-sm-5">
                            <div class="text-center">
                                <h2 class="color-primary-12 fs-4 fw-semibold title-blog-item pt-4">
                                    {{ $blog->title }}
                                </h2>

                                <div class="my-2 d-flex justify-content-center align-items-center info-blog">
                                    @if ($blog->create_by)
                                        <span class="color-primary-13">By <span
                                                class="fw-semibold">{{ $blog->create_by }}</span></span>
                                    @else
                                        <span class="color-primary-13">By <span
                                                class="fw-semibold">{{ $blog->user->full_name }}</span></span>
                                    @endif
                                    <span class="color-primary-13 fs-3">•</span>
                                    <span class="d-flex align-items-center">
                                        <img class="img-info-blog me-1" src="{{ asset('images/svg/blogs/time-blue.svg') }}"
                                            alt="time">
                                        <span id="blogDate">{{ $blog->created_at->format('d/m/Y') }}</span>
                                    </span>
                                    <span class="d-flex align-items-center">
                                        <img class="img-info-blog me-1" src="{{ asset('images/svg/blogs/view-blue.svg') }}"
                                            alt="view">
                                        <span id="blogViews">{{ $blog->views }}</span>
                                    </span>
                                </div>
                            </div>

                            <p class="fst-italic color-primary-12 text-md text-justify">
                                {!! $blog->subtitle !!}
                            </p>

                            @if (count($tableOfContents) > 0)
                                <div class="px-md-5">
                                    <div class="menu-content-main px-2 px-md-5 py-2">
                                        <span class="fw-semibold">
                                            <img class="me-3" src="{{ asset('images/svg/blogs/menu-main.svg') }}"
                                                alt="">
                                            Nội dung chính bài viết
                                        </span>
                                        <div class="mt-3">
                                            <ol class="blog-list mb-0">
                                                @foreach ($tableOfContents as $item)
                                                    <li>
                                                        <a href="#{{ $item['slug'] }}"
                                                            class="toc-link text-decoration-none color-primary-12">
                                                            {{ $item['text'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 blog-content">
                                {!! $blogContent !!}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="mt-4 ps-md-5 pe-md-2">
                    <x-client.about-content key="blog-item" />

                    <div class="mt-4 tag-share-blog">
                        <div class="d-flex align-items-start">
                            <span class="tags-blog-item px-1 me-2 text-xs-2">
                                <img class="me-1" src="{{ asset('images/svg/search-results/tag.svg') }}" alt="">
                                Tags:
                            </span>
                            <div class="color-primary-13">
                                @foreach ($blog->tags as $tag)
                                    <span>{{ $tag->name }}</span>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                            target="_blank" rel="noopener noreferrer"
                            class="btn rounded-0 btn-share-fb p-1 fw-semibold mt-2 mt-lg-0">
                            <div class="d-flex align-items-center">
                                <img class="me-1" src="{{ asset('images/d/blogs/logo-facebook.png') }}"
                                    alt="logo-facebook">
                                Chia sẻ
                            </div>
                        </a>

                    </div>

                    <div class="mt-4">
                        <x-client.related-blogs :relatedBlogs="$relatedBlogs" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3 mt-5 pt-4">
                <x-blog-sidebar :categories="$categories" :sidebarSetting="$sidebarSetting" :sidebarBlogs="$sidebarBlogs" />

                @if ($sidebarSetting->banner_images && count($sidebarSetting->banner_images) > 0)
                    @foreach ($sidebarSetting->banner_images as $banner)
                        <div class="mt-3">
                            <img class="img-fluid w-100 img-banner-blog-item" src="{{ asset('storage/' . $banner) }}"
                                alt="Banner">
                        </div>
                    @endforeach
                @else
                    <div>
                        <img class="img-fluid w-100 img-banner-blog-item"
                            src="{{ asset('/images/d/dev/blogs/banner1.png') }}" alt="banner1">
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4 mt-md-5 px-0">
            <x-client.content-image />
        </div>


    </div>
    <div class="pt-3 pt-md-5 mt-md-5">
        <x-client.desktop desktop-image="images/d/desktops/desktop.png" background-image="images/d/desktops/background.png"
            frame-image="images/d/desktops/khung.png" alt="Desktop Screenshot" />
    </div>
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/styles-blog.css')
    @vite('resources/assets/frontend/css/blog-sidebar.css')

    <style>
        .breadcrumb {
            overflow-x: auto !important;
            overflow-y: hidden !important;
            white-space: nowrap !important;
            flex-wrap: nowrap !important;
            max-width: 100% !important;
        }

        .breadcrumb::-webkit-scrollbar {
            height: 4px;
        }

        .breadcrumb::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .breadcrumb::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }

        .breadcrumb::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .blog-list {
            overflow-x: auto !important;
            overflow-y: hidden !important;
        }

        .blog-list::-webkit-scrollbar {
            height: 4px;
        }

        .blog-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .blog-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }

        .blog-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .blog-content {
            overflow-x: hidden;
        }

        .blog-content img {
            max-width: 100% !important;
            height: auto !important;
            width: auto !important;
            display: block;
            margin: 0 auto;
        }

        .blog-content figure {
            max-width: 100% !important;
            overflow: hidden;
        }

        .blog-content figure img {
            max-width: 100% !important;
            height: auto !important;
        }

        .blog-content table {
            max-width: 100% !important;
            overflow-x: auto;
            display: block;
        }

        @media (max-width: 768px) {
            .breadcrumb {
                padding-bottom: 8px;
            }

            .blog-list {
                padding-bottom: 8px;
            }
        }
    </style>
@endpush
