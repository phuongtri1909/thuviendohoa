{{-- File: resources/views/components/badge.blade.php --}}

@props([
    'type' => 'premium', // premium, vip, gold, silver, bronze, success, danger, warning, info
    'label' => 'Premium',
    'value' => null,
    'icon' => null,
])

@php
    $colors = [
        'premium' => ['bg' => '#F0A610', 'text' => '#fff'],
        'vip' => ['bg' => '#9b59b6', 'text' => '#fff'],
        'gold' => ['bg' => '#f1c40f', 'text' => '#333'],
        'silver' => ['bg' => '#95a5a6', 'text' => '#fff'],
        'bronze' => ['bg' => '#cd7f32', 'text' => '#fff'],
        'success' => ['bg' => '#27ae60', 'text' => '#fff'],
        'danger' => ['bg' => '#e74c3c', 'text' => '#fff'],
        'warning' => ['bg' => '#f39c12', 'text' => '#fff'],
        'info' => ['bg' => '#3498db', 'text' => '#fff'],
        'primary' => ['bg' => '#2c3e50', 'text' => '#fff'],
    ];

    $color = $colors[$type] ?? $colors['premium'];
@endphp

@push('styles')
    <style>
        .custom-badge {
            display: inline-flex;
            align-items: stretch;
            font-weight: 600;
            font-size: 16px;
            height: 32px;
            position: relative;
            margin-right: 15px;
            white-space: nowrap;
        }

        .custom-badge-value {
            background-color: {{ $color['bg'] }};
            color: {{ $color['text'] }};
            padding: 0 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 4px 0 0 4px;
            font-size: 17px;
            font-weight: 700;
            min-width: 55px;
        }

        .custom-badge-divider {
            width: 2px;
            background-color: rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .custom-badge-label {
            background-color: #F0A610;
            color: #fff;
            padding: 0 40px 0 5px;
            display: flex;
            align-items: center;
            position: relative;
            font-size: 15px;
            font-weight: 600;
            min-width: 100px;
            clip-path: polygon(0 0, calc(100% - 20px) 0, 70% 50%, calc(100% - 20px) 100%, 0 100%);
        }

        .custom-badge-label::after {
            display: none;
        }

        .custom-badge-label::before {
            display: none;
        }

        .custom-badge-icon {
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .custom-badge {
                font-size: 14px;
                height: 40px;
            }

            .custom-badge-value {
                padding: 0 16px;
                font-size: 16px;
                min-width: 70px;
            }

            .custom-badge-label {
                padding: 0 35px 0 16px;
                font-size: 16px;
                min-width: 120px;
                clip-path: polygon(0 0, calc(100% - 16px) 0, 70% 50%, calc(100% - 16px) 100%, 0 100%);
            }
        }

        @media (max-width: 576px) {
            .custom-badge {
                font-size: 12px;
                height: 34px;
            }

            .custom-badge-value {
                padding: 0 12px;
                font-size: 14px;
                min-width: 60px;
            }

            .custom-badge-label {
                padding: 0 30px 0 12px;
                font-size: 14px;
                min-width: 100px;
                clip-path: polygon(0 0, calc(100% - 14px) 0, 100% 50%, calc(100% - 14px) 100%, 0 100%);
            }
        }
    </style>
@endpush

<div class="custom-badge">
    @if ($value)
        <div class="custom-badge-value">
            @if ($icon)
                <i class="{{ $icon }} custom-badge-icon"></i>
            @endif
            {{ $value }}
        </div>
        <div class="custom-badge-divider"></div>
    @endif

    <div class="custom-badge-label">
        {{ $label }}
    </div>
</div>
