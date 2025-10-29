@extends('client.layouts.app')

@section('title', $page->title)

@section('content')
    <div class="page-detail-page">
        <div class="container-custom">
            <div class="pt-0 p-2 px-lg-5 pb-lg-5  container-page-detail bg-white shadow-sm">
                <div class="pt-3">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb color-primary-12">
                            <li class="breadcrumb-item "><a class="color-primary-12 text-decoration-none"
                                    href="{{ route('home') }}">TRANG CHỦ</a></li>
                            <li class="breadcrumb-item color-primary-12 active fw-semibold" aria-current="page">{{ $page->title }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="px-0 p-lg-5">
                    <div class="text-center mb-4">
                        <h1 class="text-uppercase fw-bold fs-3 color-primary">{{ $page->title }}</h1>
                    </div>

                    <div class="page-content-wrapper mt-4">
                        <div class="page-content">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @vite('resources/assets/frontend/css/page-detail.css')
@endpush

