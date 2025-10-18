@extends('Admin.layouts.sidebar')

@section('title', 'Cài đặt hệ thống')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Cài đặt hệ thống</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-cog icon-title"></i>
                    <h5>Cài đặt hệ thống</h5>
                </div>
            </div>

            <div class="card-content">
                <div class="settings-tabs">
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        @if (auth()->user()->hasRole('admin'))
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request('tab') == 'smtp' || !request('tab') ? 'active' : '' }}"
                                    id="smtp-tab" data-toggle="tab" href="#smtp" role="tab">
                                    <i class="fas fa-envelope"></i> SMTP
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasRole('admin'))
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request('tab') == 'google' ? 'active' : '' }}" id="google-tab"
                                    data-toggle="tab" href="#google" role="tab">
                                    <i class="fab fa-google"></i> Google
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasRole('admin'))
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request('tab') == 'facebook' ? 'active' : '' }}" id="facebook-tab"
                                    data-toggle="tab" href="#facebook" role="tab">
                                    <i class="fab fa-facebook"></i> Facebook
                                </a>
                            </li>
                        @endif
                    </ul>

                    <div class="tab-content mt-4" id="settingsTabContent">

                        <div class="tab-pane fade {{ request('tab') == 'smtp' || !request('tab') ? 'show active' : '' }}"
                            id="smtp" role="tabpanel">
                            <form action="{{ route('admin.setting.update.smtp') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="mailer">Mailer</label>
                                    <input type="text" id="mailer" name="mailer" class="form-control"
                                        value="{{ $smtpSetting->mailer ?? 'smtp' }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="host">Host</label>
                                    <input type="text" id="host" name="host" class="form-control"
                                        value="{{ $smtpSetting->host ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="port">Port</label>
                                    <input type="text" id="port" name="port" class="form-control"
                                        value="{{ $smtpSetting->port ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" class="form-control"
                                        value="{{ $smtpSetting->username ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" class="form-control"
                                        value="{{ $smtpSetting->password ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="encryption">Encryption</label>
                                    <select id="encryption" name="encryption" class="form-control">
                                        <option value="">None</option>
                                        <option value="tls"
                                            {{ ($smtpSetting->encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS
                                        </option>
                                        <option value="ssl"
                                            {{ ($smtpSetting->encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="from_address">From Address</label>
                                    <input type="email" id="from_address" name="from_address" class="form-control"
                                        value="{{ $smtpSetting->from_address ?? '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="from_name">From Name</label>
                                    <input type="text" id="from_name" name="from_name" class="form-control"
                                        value="{{ $smtpSetting->from_name ?? '' }}">
                                </div>

                                <div class="form-group">
                                    <label for="admin_email">Admin Email</label>
                                    @if ($smtpSetting->admin_email && $smtpSetting->admin_email !== auth()->user()->email)
                                        <input type="email" id="admin_email" name="admin_email" class="form-control"
                                            value="{{ $smtpSetting->admin_email ?? '' }}" readonly>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock"></i> Chỉ {{ $smtpSetting->admin_email }} mới có
                                            quyền
                                            chỉnh sửa
                                        </small>
                                    @else
                                        <input type="email" id="admin_email" name="admin_email" class="form-control"
                                            value="{{ $smtpSetting->admin_email ?? '' }}" required>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-crown"></i> Email này sẽ có quyền cao nhất trong hệ
                                            thống
                                        </small>
                                    @endif
                                </div>


                                <div class="form-actions">
                                    <button type="submit" class="action-button">
                                        <i class="fas fa-save"></i> Lưu cài đặt SMTP
                                    </button>
                                </div>
                            </form>

                        </div>

                        <div class="tab-pane fade {{ request('tab') == 'google' ? 'show active' : '' }}" id="google"
                            role="tabpanel">
                            <form action="{{ route('admin.setting.update.google') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="google_client_id">Client ID</label>
                                    <input type="text" id="google_client_id" name="google_client_id"
                                        class="form-control" value="{{ $googleSetting->google_client_id ?? '' }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="google_client_secret">Client Secret</label>
                                    <input type="password" id="google_client_secret" name="google_client_secret"
                                        class="form-control" value="{{ $googleSetting->google_client_secret ?? '' }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="facebook_redirect">Redirect URI</label>
                                    <p class="form-control-plaintext">
                                        {{ route($googleSetting->google_redirect ?? 'auth.google.callback') }}
                                    </p>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Đường dẫn callback cho Google OAuth
                                    </small>
                                </div>



                                <div class="form-actions">
                                    <button type="submit" class="action-button">
                                        <i class="fas fa-save"></i> Lưu cài đặt Google
                                    </button>
                                </div>
                            </form>

                        </div>

                        <div class="tab-pane fade {{ request('tab') == 'facebook' ? 'show active' : '' }}" id="facebook"
                            role="tabpanel">
                            <form action="{{ route('admin.setting.update.facebook') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="facebook_client_id">Client ID</label>
                                    <input type="text" id="facebook_client_id" name="facebook_client_id"
                                        class="form-control" value="{{ $facebookSetting->facebook_client_id ?? '' }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="facebook_client_secret">Client Secret</label>
                                    <input type="password" id="facebook_client_secret" name="facebook_client_secret"
                                        class="form-control" value="{{ $facebookSetting->facebook_client_secret ?? '' }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="facebook_redirect">Redirect URI</label>
                                    <p class="form-control-plaintext">
                                        {{ route($facebookSetting->facebook_redirect ?? 'auth.facebook.callback') }}
                                    </p>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Đường dẫn callback cho Facebook OAuth
                                    </small>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="action-button">
                                        <i class="fas fa-save"></i> Lưu cài đặt Facebook
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('styles')
        <style>
            .settings-tabs .nav-tabs {
                border-bottom: 1px solid #dee2e6;
            }

            .settings-tabs .nav-link {
                color: #495057;
                background-color: #fff;
                border: 1px solid transparent;
                border-top-left-radius: 0.25rem;
                border-top-right-radius: 0.25rem;
                padding: 0.5rem 1rem;
                margin-right: 0.25rem;
            }

            .settings-tabs .nav-link.active {
                color: #007bff;
                background-color: #fff;
                border-color: #dee2e6 #dee2e6 #fff;
                font-weight: bold;
            }

            .settings-tabs .nav-link i {
                margin-right: 5px;
            }

            .settings-section {
                margin-bottom: 2rem;
                padding: 1rem;
                background-color: #f9f9f9;
                border-radius: 0.25rem;
            }

            .settings-section h4 {
                margin-bottom: 1rem;
                color: #333;
                border-bottom: 1px solid #ddd;
                padding-bottom: 0.5rem;
            }



            .form-text {
                margin-top: 0.25rem;
                font-size: 0.85em;
                color: #6c757d;
            }

            .setting-key {
                font-family: monospace;
                background-color: #f5f5f5;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 0.9em;
                color: #555;
            }

            .form-actions {
                margin-top: 20px;
                display: flex;
                justify-content: flex-end;
            }

            .action-button {
                display: inline-flex;
                align-items: center;
                padding: 8px 16px;
                background-color: #007bff;
                color: #fff;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
            }

            .action-button i {
                margin-right: 5px;
            }

            .action-button:hover {
                background-color: #0069d9;
            }

            .custom-control {
                position: relative;
                display: block;
                min-height: 1.5rem;
                padding-left: 1.5rem;
            }

            .custom-control-input {
                position: absolute;
                z-index: -1;
                opacity: 0;
            }

            .custom-control-label {
                position: relative;
                margin-bottom: 0;
                vertical-align: top;
                cursor: pointer;
            }

            .custom-control-label::before {
                position: absolute;
                top: 0.25rem;
                left: -1.5rem;
                display: block;
                width: 1rem;
                height: 1rem;
                content: "";
                background-color: #fff;
                border: 1px solid #adb5bd;
                border-radius: 0.25rem;
            }

            .custom-control-input:checked~.custom-control-label::before {
                color: #fff;
                border-color: #007bff;
                background-color: #007bff;
            }

            .alert {
                padding: 12px 15px;
                margin-bottom: 20px;
                border-radius: 4px;
            }

            .alert-success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                function activateTab(tabId) {
                    $('.tab-pane').removeClass('show active');

                    $('#' + tabId).addClass('show active');

                    $('.nav-tabs .nav-link').removeClass('active');
                    $('.nav-tabs a[href="#' + tabId + '"]').addClass('active');
                }

                var activeTabParam = window.location.search.match(/tab=([^&]*)/);
                if (activeTabParam) {
                    activateTab(activeTabParam[1]);
                }

                $('.nav-tabs a').on('click', function(e) {
                    e.preventDefault();
                    var tabId = $(this).attr('href').substr(1);

                    activateTab(tabId);

                    history.replaceState(null, null, '?tab=' + tabId);
                });
            });
        </script>
    @endpush
