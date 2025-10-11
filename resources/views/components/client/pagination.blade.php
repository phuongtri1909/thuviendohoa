{{-- File: resources/views/components/pagination.blade.php --}}

@props(['paginator', 'showSelect' => true, 'maxVisible' => 5])

@php
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $pages = [];

    if ($lastPage <= $maxVisible) {
        for ($i = 1; $i <= $lastPage; $i++) {
            $pages[] = $i;
        }
    } else {
        $leftOffset = floor($maxVisible / 2);
        $rightOffset = $maxVisible - $leftOffset - 1;

        $start = max(1, $currentPage - $leftOffset);
        $end = min($lastPage, $currentPage + $rightOffset);

        if ($currentPage <= $leftOffset) {
            $end = min($lastPage, $maxVisible);
        }
        if ($currentPage >= $lastPage - $rightOffset) {
            $start = max(1, $lastPage - $maxVisible + 1);
        }

        if ($start > 1) {
            $pages[] = 1;
            if ($start > 2) {
                $pages[] = '...';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }

        if ($end < $lastPage) {
            if ($end < $lastPage - 1) {
                $pages[] = '...';
            }
            $pages[] = $lastPage;
        }
    }
@endphp

@if ($paginator->hasPages())
    @push('styles')
        <style>
            .pagination-wrapper {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
                flex-wrap: wrap;
                margin: 20px 0;
            }

            .pagination {
                margin: 0;
            }

            .page-link {
                border-radius: 4px;
                margin: 0 2px;
                border: 1px solid #dee2e6;
                color: #333;
                padding: 8px 12px;
                text-decoration: none;
            }

            .page-link:hover {
                background-color: #e9ecef;
                border-color: #dee2e6;
                color: #333;
            }

            .page-item.active .page-link {
                background-color: #dc3545;
                border-color: #dc3545;
                color: white;
                font-weight: 500;
                cursor: default;
            }

            .page-item.disabled .page-link {
                background-color: #fff;
                border-color: #dee2e6;
                color: #6c757d;
                cursor: not-allowed;
            }

            .page-info {
                display: flex;
                align-items: center;
                gap: 8px;
                white-space: nowrap;
            }

            .page-select {
                width: 70px;
                padding: 6px 8px;
                border: 1px solid #ced4da;
                border-radius: 4px;
                font-size: 14px;
            }

            .page-select:focus {
                border-color: #86b7fe;
                outline: 0;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
            }

            @media (max-width: 576px) {
                .pagination-wrapper {
                    gap: 10px;
                }

                .page-link {
                    padding: 6px 10px;
                    font-size: 14px;
                }

                .page-info {
                    font-size: 14px;
                }

                .page-select {
                    width: 60px;
                }
            }
        </style>
    @endpush

    <div class="pagination-wrapper">
        <nav>
            <ul class="pagination">
                {{-- Previous Button --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    @if ($paginator->onFirstPage())
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif
                </li>

                {{-- Page Numbers --}}
                @foreach ($pages as $page)
                    @if ($page === '...')
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @else
                        <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                            @if ($page == $currentPage)
                                <span class="page-link">{{ $page }}</span>
                            @else
                                <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endif
                @endforeach

                {{-- Next Button --}}
                <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                    @if ($paginator->hasMorePages())
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </li>
            </ul>
        </nav>

        {{-- Page Select & Info --}}
        @if ($showSelect)
            <div class="page-info">
                <span>Trang</span>
                <select class="page-select" onchange="window.location.href = this.value">
                    @for ($i = 1; $i <= $lastPage; $i++)
                        <option value="{{ $paginator->url($i) }}" {{ $i == $currentPage ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                <span>/ {{ $lastPage }}</span>
            </div>
        @endif
    </div>
@endif

{{-- Không hiển thị dropdown chọn trang --}}
{{-- <x-client.pagination :paginator="$paginator" :show-select="false" /> --}}

{{-- Thay đổi số trang hiển thị tối đa (mặc định 5) --}}
{{-- <x-client.pagination :paginator="$paginator" :max-visible="7" /> --}}

{{-- Kết hợp cả 2 --}}
{{-- <x-client.pagination 
    :paginator="$paginator" 
    :show-select="false" 
    :max-visible="7" 
/> --}}