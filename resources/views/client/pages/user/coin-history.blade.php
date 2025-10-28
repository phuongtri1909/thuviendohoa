@extends('client.layouts.information')

@section('info_title', 'Lịch sử giao dịch')
@section('info_description', 'Lịch sử giao dịch xu của bạn trên ' . request()->getHost())
@section('info_keyword', 'Lịch sử giao dịch, xu, ' . request()->getHost())
@section('info_section_title', 'Lịch sử giao dịch xu')
@section('info_section_desc', 'Theo dõi tất cả các giao dịch xu của bạn')

@push('styles')
    <style>
        .coin-history-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .coin-history-header {
            background: var(--bg-gradient);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .coin-history-header h4 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .coin-history-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .coin-history-list {
            padding: 0;
        }

        .coin-history-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .coin-history-item:last-child {
            border-bottom: none;
        }

        .coin-history-item:hover {
            background: #f8f9fa;
        }

        .coin-history-item.unread {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }

        .coin-history-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 20px;
            color: white;
        }

        .coin-history-icon.getlink {
            background: var(--bg-gradient);
        }

        .coin-history-icon.payment {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .coin-history-icon.purchase {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }

        .coin-history-icon.manual {
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
        }

        .coin-history-icon.monthly_bonus {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        }

        .coin-history-content {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .coin-history-info {
            flex: 1;
        }

        .coin-history-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .coin-history-desc {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .coin-history-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 12px;
            color: #999;
        }

        .coin-history-amount {
            text-align: right;
        }

        .coin-history-amount.positive {
            color: #28a745;
            font-weight: 600;
            font-size: 18px;
        }

        .coin-history-amount.negative {
            color: #dc3545;
            font-weight: 600;
            font-size: 18px;
        }

        .coin-history-date {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        .coin-history-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
        }

        .coin-history-status.read {
            color: #6c757d;
        }

        .coin-history-status.unread {
            color: #ffc107;
        }

        .mark-all-read-btn {
            background: var(--bg-gradient);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mark-all-read-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .empty-state-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .empty-state-desc {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .empty-state-btn {
            background: var(--bg-gradient);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .empty-state-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
            color: white;
        }

        @media (max-width: 768px) {
            .coin-history-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .coin-history-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
                width: 100%;
            }

            .coin-history-amount {
                text-align: left;
            }
        }
    </style>
@endpush

@section('info_content')
    @if($coinHistories->count() > 0)
        <div class="coin-history-table">
            <div class="coin-history-list">
                @foreach($coinHistories as $history)
                    <div class="coin-history-item {{ !$history->is_read ? 'unread' : '' }}" data-history-id="{{ $history->id }}">
                        <div class="coin-history-icon {{ $history->type }}">
                            @switch($history->type)
                                @case('payment')
                                    <i class="fas fa-credit-card"></i>
                                    @break
                                @case('purchase')
                                    <i class="fas fa-shopping-cart"></i>
                                    @break
                                @case('manual')
                                    <i class="fas fa-user-cog"></i>
                                    @break
                                @case('monthly_bonus')
                                    <i class="fas fa-gift"></i>
                                    @break
                                @default
                                    <i class="fas fa-coins"></i>
                            @endswitch
                        </div>
                        
                        <div class="coin-history-content">
                            <div class="coin-history-info">
                                <div class="coin-history-title">{{ $history->reason }}</div>
                                @if($history->description)
                                    <div class="coin-history-desc">{{ $history->description }}</div>
                                @endif
                                
                                <div class="coin-history-meta">
                                    <div class="coin-history-date">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $history->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    
                                    <div class="coin-history-status {{ $history->is_read ? 'read' : 'unread' }}">
                                        <i class="fas {{ $history->is_read ? 'fa-check-circle' : 'fa-circle' }}"></i>
                                        {{ $history->is_read ? 'Đã đọc' : 'Chưa đọc' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="coin-history-amount {{ $history->amount > 0 ? 'positive' : 'negative' }}">
                                {{ $history->amount > 0 ? '+' : '' }}{{ number_format($history->amount) }} xu
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <x-client.pagination :paginator="$coinHistories" />
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-coins"></i>
            </div>
            <h3 class="empty-state-title">Chưa có giao dịch nào</h3>
            <p class="empty-state-desc">
                Bạn chưa có giao dịch xu nào. Hãy nạp xu hoặc mua file để có lịch sử giao dịch!
            </p>
            <a href="{{ route('user.payment') }}" class="empty-state-btn">
                <i class="fas fa-credit-card me-2"></i>Nạp xu ngay
            </a>
        </div>
    @endif
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            // Mark as read when clicking on history item
            $('.coin-history-item').on('click', function() {
                const historyId = $(this).data('history-id');
                const $item = $(this);
                
                if (!$item.hasClass('unread')) return;
                
                // Mark as read via AJAX
                $.post('{{ route("user.coin-history.mark-read") }}', {
                    id: historyId,
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        $item.removeClass('unread');
                        $item.find('.coin-history-status').removeClass('unread').addClass('read');
                        $item.find('.coin-history-status i').removeClass('fa-circle').addClass('fa-check-circle');
                        $item.find('.coin-history-status').text('Đã đọc');
                        
                        // Update notification count
                        updateNotificationCount();
                    }
                });
            });
        });
        
        function updateNotificationCount() {
            $.get('{{ route("user.coin-history.unread-count") }}', function(response) {
                const count = response.count;
                const $badge = $('.notification-badge');
                
                if (count > 0) {
                    $badge.text(count).show();
                } else {
                    $badge.hide();
                }
            });
        }
    </script>
@endpush
