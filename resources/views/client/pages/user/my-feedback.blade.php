@extends('client.layouts.information')

@section('info_title', 'Góp ý của tôi')
@section('info_description', 'Danh sách góp ý của bạn trên ' . request()->getHost())
@section('info_keyword', 'Góp ý, phản hồi, ' . request()->getHost())
@section('info_section_title', 'Góp ý của tôi')
@section('info_section_desc', 'Theo dõi tất cả các góp ý và phản hồi của bạn')

@push('styles')
    <style>
        .feedback-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .feedback-list {
            padding: 0;
        }

        .feedback-item {
            display: flex;
            align-items: flex-start;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .feedback-item:last-child {
            border-bottom: none;
        }

        .feedback-item:hover {
            background: #f8f9fa;
        }

        .feedback-item.has-reply {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }

        .feedback-item.pending {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
        }

        .feedback-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 20px;
            color: white;
            flex-shrink: 0;
        }

        .feedback-icon.pending {
            background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%);
        }

        .feedback-icon.read {
            background: linear-gradient(135deg, #2196f3 0%, #03a9f4 100%);
        }

        .feedback-icon.replied {
            background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
        }

        .feedback-content {
            flex: 1;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .feedback-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .feedback-status {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .feedback-status.pending {
            background: #fff3e0;
            color: #e65100;
        }

        .feedback-status.read {
            background: #e3f2fd;
            color: #1976d2;
        }

        .feedback-status.replied {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .feedback-message {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .feedback-reply {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #4caf50;
        }

        .feedback-reply-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #4caf50;
            font-weight: 600;
        }

        .feedback-reply-content {
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }

        .feedback-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 12px;
            color: #999;
            margin-top: 10px;
        }

        .feedback-date {
            display: flex;
            align-items: center;
            gap: 5px;
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

        @media (max-width: 768px) {
            .feedback-item {
                flex-direction: column;
                gap: 15px;
            }

            .feedback-icon {
                margin-right: 0;
            }

            .feedback-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .feedback-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
@endpush

@section('info_content')
    @if($feedbacks->count() > 0)
        <div class="feedback-table">
            <div class="feedback-list">
                @foreach($feedbacks as $feedback)
                    <div class="feedback-item {{ $feedback->status === 'replied' ? 'has-reply' : ($feedback->status === 'pending' ? 'pending' : '') }}">
                        <div class="feedback-icon {{ $feedback->status }}">
                            @switch($feedback->status)
                                @case('pending')
                                    <i class="fas fa-clock"></i>
                                    @break
                                @case('read')
                                    <i class="fas fa-eye"></i>
                                    @break
                                @case('replied')
                                    <i class="fas fa-check-circle"></i>
                                    @break
                                @default
                                    <i class="fas fa-comment"></i>
                            @endswitch
                        </div>
                        
                        <div class="feedback-content">
                            <div class="feedback-header">
                                <div class="feedback-title">Góp ý #{{ $feedback->id }}</div>
                                <span class="feedback-status {{ $feedback->status }}">
                                    @switch($feedback->status)
                                        @case('pending')
                                            <i class="fas fa-clock"></i>
                                            Chờ xử lý
                                            @break
                                        @case('read')
                                            <i class="fas fa-eye"></i>
                                            Đã đọc
                                            @break
                                        @case('replied')
                                            <i class="fas fa-check-circle"></i>
                                            Đã phản hồi
                                            @break
                                    @endswitch
                                </span>
                            </div>
                            
                            <div class="feedback-message">
                                {{ $feedback->message }}
                            </div>
                            
                            @if($feedback->status === 'replied' && $feedback->admin_reply)
                                <div class="feedback-reply">
                                    <div class="feedback-reply-header">
                                        <i class="fas fa-reply"></i>
                                        <span>Phản hồi từ Admin {{ $feedback->admin ? '(' . $feedback->admin->full_name . ')' : '' }}</span>
                                    </div>
                                    <div class="feedback-reply-content">
                                        {{ $feedback->admin_reply }}
                                    </div>
                                    <div class="feedback-meta">
                                        <div class="feedback-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $feedback->replied_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="feedback-meta">
                                <div class="feedback-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    Gửi lúc: {{ $feedback->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <x-client.pagination :paginator="$feedbacks" />
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-comments"></i>
            </div>
            <h3 class="empty-state-title">Chưa có góp ý nào</h3>
            <p class="empty-state-desc">
                Bạn chưa gửi góp ý nào. Hãy gửi góp ý của bạn để chúng tôi cải thiện dịch vụ!
            </p>
        </div>
    @endif
@endsection

