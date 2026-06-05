<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ config('app.name', 'alxzen') }} - @yield('title')</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="_token" content="{{ csrf_token() }}">

        <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="/favicons/manifest.json">
        <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#bc6e3c">
        <link rel="shortcut icon" href="/favicons/favicon.ico">
        <meta name="msapplication-config" content="/favicons/browserconfig.xml">
        <meta name="theme-color" content="#0e4688">
                  <style>
            /* 1. Warna Dasar & Sidebar (Hitam & Ungu) */
            .main-sidebar {
                background-color: #0a0a0c !important; /* Hitam Pekat */
            }
            .sidebar-menu > li.header {
                background: #000000 !important;
                color: #8e44ad !important; /* Ungu Header */
                font-weight: 800 !important;
                letter-spacing: 1px;
            }

            /* 2. Menu Sidebar (Font Tebal & Hover Ungu) */
            .sidebar-menu > li > a {
                font-weight: 600 !important; /* Tebalkan Font Menu */
                border-left: 3px solid transparent;
                transition: all 0.3s ease;
            }
            .sidebar-menu > li:hover > a, .sidebar-menu > li.active > a {
                background: #1a1a20 !important;
                color: #a29bfe !important; /* Ungu Muda */
                border-left-color: #6c5ce7 !important; /* Garis Ungu di samping */
            }

            /* 3. Navbar Atas & Logo */
            .main-header .logo {
                background-color: #000000 !important;
                color: #a29bfe !important;
                font-weight: 800 !important;
                border-bottom: 1px solid #1a1a20;
            }
            .main-header .navbar {
                background-color: #0a0a0c !important;
                border-bottom: 1px solid #1a1a20;
            }

            /* 4. Body Content (Hitam Sedikit Ungu) */
            .content-wrapper {
                background-color: #111116 !important;
            }
            .content-header h1 {
                font-weight: 800 !important;
                color: #ffffff;
                text-shadow: 0 0 10px rgba(162, 155, 254, 0.2);
            }

            /* 5. Box / Panel (Modern Dark KDE Plasma) */
            .box {
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%) !important;
                border: 1px solid rgba(99, 102, 241, 0.2) !important;
                border-radius: 12px !important;
                box-shadow: 0 4px 24px rgba(0,0,0,0.4) !important;
                color: #ffffff !important;
                overflow: hidden !important;
                margin-bottom: 20px;
            }
            .box-header {
                color: #ffffff !important;
                padding: 18px 24px !important;
                border-bottom: 1px solid rgba(99, 102, 241, 0.15) !important;
                background: rgba(99, 102, 241, 0.05) !important;
            }
            .box-title {
                font-size: 15px !important;
                font-weight: 600 !important;
                color: #e2e8f0 !important;
                letter-spacing: 0.3px;
            }
            .box-body { padding: 20px !important; }
            .box-footer {
                background: rgba(15, 23, 42, 0.6) !important;
                border-top: 1px solid rgba(99,102,241,0.2) !important;
                padding: 15px 20px !important;
            }

            /* 6. Forms & Inputs (Glassmorphism) */
            .form-control {
                background: rgba(15, 23, 42, 0.6) !important;
                border: 1px solid rgba(99,102,241,0.3) !important;
                border-radius: 8px !important;
                color: #cbd5e1 !important;
                transition: all 0.2s;
                box-shadow: inset 0 1px 2px rgba(0,0,0,0.1) !important;
            }
            .form-control:focus {
                border-color: #818cf8 !important;
                box-shadow: 0 0 0 3px rgba(129,140,248,0.15) !important;
                background: rgba(15, 23, 42, 0.8) !important;
                outline: none;
            }
            .form-control[disabled], .form-control[readonly] {
                background: rgba(0,0,0,0.2) !important;
                color: #64748b !important;
                cursor: not-allowed;
            }
            .input-group-addon {
                background: rgba(99,102,241,0.1) !important;
                border: 1px solid rgba(99,102,241,0.3) !important;
                color: #818cf8 !important;
                border-radius: 8px 0 0 8px !important;
            }
            label { color: #94a3b8 !important; font-weight: 600 !important; font-size: 13px !important; }

            /* 7. Tables (Modern Dark) */
            .table { border-collapse: collapse !important; width: 100% !important; margin-bottom: 0 !important; }
            .table > thead > tr > th {
                background: rgba(15, 23, 42, 0.6) !important;
                border-bottom: 1px solid rgba(99,102,241,0.2) !important;
                padding: 12px 20px !important;
                font-size: 11px !important;
                font-weight: 600 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.8px !important;
                color: #64748b !important;
            }
            .table > tbody > tr > td {
                border-bottom: 1px solid rgba(255,255,255,0.04) !important;
                border-top: none !important;
                padding: 14px 20px !important;
                font-size: 13px !important;
                color: #cbd5e1 !important;
                vertical-align: middle !important;
            }
            .table-hover > tbody > tr:hover { background: rgba(99,102,241,0.06) !important; }

            /* 8. Tombol & Badge (Neon & Gradient) */
            .btn {
                font-weight: 600 !important;
                border-radius: 6px !important;
                font-size: 12px !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                transition: all 0.2s !important;
                border: none !important;
                padding: 8px 16px !important;
            }
            .btn-primary {
                background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
                color: #fff !important;
                box-shadow: 0 2px 10px rgba(99,102,241,0.3) !important;
            }
            .btn-primary:hover {
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 15px rgba(99,102,241,0.5) !important;
            }
            .btn-success {
                background: linear-gradient(135deg, #10b981, #059669) !important;
                color: #fff !important;
                box-shadow: 0 2px 10px rgba(16,185,129,0.3) !important;
            }
            .btn-danger {
                background: linear-gradient(135deg, #ef4444, #dc2626) !important;
                color: #fff !important;
                box-shadow: 0 2px 10px rgba(239,68,68,0.3) !important;
            }
            .btn-warning {
                background: linear-gradient(135deg, #f59e0b, #d97706) !important;
                color: #fff !important;
            }
            .btn-default {
                background: rgba(255,255,255,0.05) !important;
                color: #cbd5e1 !important;
                border: 1px solid rgba(255,255,255,0.1) !important;
            }
            .btn-default:hover { background: rgba(255,255,255,0.1) !important; }
            .label { font-weight: 700 !important; border-radius: 4px !important; padding: 3px 8px !important; }
            .label-primary { background: rgba(99,102,241,0.2) !important; color: #818cf8 !important; border: 1px solid rgba(99,102,241,0.4) !important; }
            .label-success { background: rgba(16,185,129,0.2) !important; color: #34d399 !important; border: 1px solid rgba(16,185,129,0.4) !important; }
            .label-danger { background: rgba(239,68,68,0.2) !important; color: #f87171 !important; border: 1px solid rgba(239,68,68,0.4) !important; }
            .label-warning { background: rgba(245,158,11,0.2) !important; color: #fbbf24 !important; border: 1px solid rgba(245,158,11,0.4) !important; }

            /* Footer */
            .main-footer {
                background: #0a0a0c !important;
                border-top: 1px solid #1a1a20 !important;
                color: #555 !important;
            }

            /* Breadcrumb / Page Header */
            .content-header > .breadcrumb {
                background: rgba(15, 23, 42, 0.6) !important;
                border: 1px solid rgba(99,102,241,0.1) !important;
                border-radius: 20px !important;
            }
            .content-header > .breadcrumb > li > a { color: #818cf8 !important; }
            .content-header > .breadcrumb > .active { color: #94a3b8 !important; }
        </style>
    </head>

        @include('layouts.scripts')

        @section('scripts')
            {!! Theme::css('vendor/select2/select2.min.css?t={cache-version}') !!}
            {!! Theme::css('vendor/bootstrap/bootstrap.min.css?t={cache-version}') !!}
            {!! Theme::css('vendor/adminlte/admin.min.css?t={cache-version}') !!}
            {!! Theme::css('vendor/adminlte/colors/skin-blue.min.css?t={cache-version}') !!}
            {!! Theme::css('vendor/sweetalert/sweetalert.min.css?t={cache-version}') !!}
            {!! Theme::css('vendor/animate/animate.min.css?t={cache-version}') !!}
            {!! Theme::css('css/pterodactyl.css?t={cache-version}') !!}
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

            @show
    </head>
    <body class="hold-transition skin-blue fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <a href="{{ route('index') }}" class="logo">
                    @if(config('app.logo'))
                        <img src="{{ config('app.logo') }}" alt="Logo" style="max-height: 35px; max-width: 100%; vertical-align: middle;">
                    @else
                        <span>{{ config('app.name', 'alxzen') }}</span>
                    @endif
                </a>
                <nav class="navbar navbar-static-top">
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li class="user-menu">
                                <a href="{{ route('account') }}">
                                    <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(Auth::user()->email)) }}?s=160" class="user-image" alt="User Image">
                                    <span class="hidden-xs">{{ Auth::user()->name_first }} {{ Auth::user()->name_last }}</span>
                                </a>
                            </li>
                            <li><a href="{{ route('index') }}" data-toggle="tooltip" data-placement="bottom" title="Exit Admin Control"><i class="fa fa-server"></i></a></li>
                            <li><a href="{{ route('auth.logout') }}" id="logoutButton" data-toggle="tooltip" data-placement="bottom" title="Logout"><i class="fa fa-sign-out"></i></a></li>
                        </ul>
                    </div>
                </nav>
            </header>
            <aside class="main-sidebar">
                <section class="sidebar">
                    <ul class="sidebar-menu" data-widget="tree">
                        <li class="header">BASIC ADMINISTRATION</li>
                        <li class="{{ Route::currentRouteName() !== 'admin.index' ?: 'active' }}">
                            <a href="{{ route('admin.index') }}">
                                <i class="fa fa-home"></i> <span>Overview</span>
                            </a>
                        </li>

                        {{-- HANYA OWNER ID 1 YANG BISA LIHAT SETTINGS & API --}}
                        @if(Auth::user()->id === 1)
                        <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.settings') ?: 'active' }}">
                            <a href="{{ route('admin.settings')}}">
                                <i class="fa fa-wrench"></i> <span>Settings</span>
                            </a>
                        </li>
                        <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.api') ?: 'active' }}">
                            <a href="{{ route('admin.api.index')}}">
                                <i class="fa fa-gamepad"></i> <span>Application API</span>
                            </a>
                        </li>
                        <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.announcements') ?: 'active' }}">
                            <a href="{{ route('admin.announcements')}}">
                                <i class="fa fa-bullhorn"></i> <span>Announcements</span>
                            </a>
                        </li>
                        @endif

                        <li class="treeview {{ starts_with(Route::currentRouteName(), ['admin.databases', 'admin.locations', 'admin.nodes']) ? 'active' : '' }}">
                            <a href="#">
                                <i class="fa fa-cogs"></i> <span>Management</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                {{-- DATABASE HANYA OWNER --}}
                                @if(Auth::user()->id === 1)
                                <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.databases') ?: 'active' }}">
                                    <a href="{{ route('admin.databases') }}">
                                        <i class="fa fa-database"></i> <span>Databases</span>
                                    </a>
                                </li>
                                <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.locations') ?: 'active' }}">
                                    <a href="{{ route('admin.locations') }}">
                                        <i class="fa fa-globe"></i> <span>Locations</span>
                                    </a>
                                </li>
                                <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.nodes') ?: 'active' }}">
                                    <a href="{{ route('admin.nodes') }}">
                                        <i class="fa fa-sitemap"></i> <span>Nodes</span>
                                    </a>
                                </li>
                                @endif

                                <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.servers') ?: 'active' }}">
                                    <a href="{{ route('admin.servers') }}">
                                        <i class="fa fa-server"></i> <span>Servers</span>
                                    </a>
                                </li>
                                
                                {{-- EXPIRATION & USERS HANYA OWNER --}}
                                @if(Auth::user()->id === 1)
                                <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.expiration') ?: 'active' }}">
                                    <a href="{{ route('admin.expiration') }}">
                                        <i class="fa fa-clock-o"></i> <span>Expiration Manager</span>
                                    </a>
                                </li>
                                <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.users') ?: 'active' }}">
                                    <a href="{{ route('admin.users') }}">
                                        <i class="fa fa-users"></i> <span>Users</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>

                        @if(Auth::user()->id === 1)
                        <li class="treeview {{ starts_with(Route::currentRouteName(), ['admin.mounts', 'admin.nests']) ? 'active' : '' }}">
                            <a href="#">
                                <i class="fa fa-server"></i> <span>Service Management</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.mounts') ?: 'active' }}">
                                    <a href="{{ route('admin.mounts') }}">
                                        <i class="fa fa-magic"></i> <span>Mounts</span>
                                    </a>
                                </li>
                                <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.nests') ?: 'active' }}">
                                    <a href="{{ route('admin.nests') }}">
                                        <i class="fa fa-th-large"></i> <span>Nests</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </section>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    @yield('content-header')
                </section>
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    There was an error validating the data provided.<br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @foreach (Alert::getMessages() as $type => $messages)
                                @foreach ($messages as $message)
                                    <div class="alert alert-{{ $type }} alert-dismissable" role="alert">
                                        {{ $message }}
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    @yield('content')
                </section>
            </div>
            <footer class="main-footer">
                <div class="pull-right small text-gray" style="margin-right:10px;margin-top:-7px;">
                    <strong><i class="fa fa-fw {{ $appIsGit ? 'fa-git-square' : 'fa-code-fork' }}"></i></strong> {{ $appVersion }}<br />
                    <strong><i class="fa fa-fw fa-clock-o"></i></strong> {{ round(microtime(true) - LARAVEL_START, 3) }}s
                </div>
                Copyright &copy; 2015 - {{ date('Y') }} <a href="https://github.com/alxzy-group/alxzen">alxzen Software</a>.
            </footer>
        </div>
        @section('footer-scripts')
            <script src="/js/keyboard.polyfill.js" type="application/javascript"></script>
            <script>keyboardeventKeyPolyfill.polyfill();</script>

            {!! Theme::js('vendor/jquery/jquery.min.js?t={cache-version}') !!}
            {!! Theme::js('vendor/sweetalert/sweetalert.min.js?t={cache-version}') !!}
            {!! Theme::js('vendor/bootstrap/bootstrap.min.js?t={cache-version}') !!}
            {!! Theme::js('vendor/slimscroll/jquery.slimscroll.min.js?t={cache-version}') !!}
            {!! Theme::js('vendor/adminlte/app.min.js?t={cache-version}') !!}
            {!! Theme::js('vendor/bootstrap-notify/bootstrap-notify.min.js?t={cache-version}') !!}
            {!! Theme::js('vendor/select2/select2.full.min.js?t={cache-version}') !!}
            {!! Theme::js('js/admin/functions.js?t={cache-version}') !!}
            <script src="/js/autocomplete.js" type="application/javascript"></script>

            @if(Auth::user()->root_admin)
                <script>
                    $('#logoutButton').on('click', function (event) {
                        event.preventDefault();

                        var that = this;
                        swal({
                            title: 'Do you want to log out?',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d9534f',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Log out'
                        }, function () {
                             $.ajax({
                                type: 'POST',
                                url: '{{ route('auth.logout') }}',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },complete: function () {
                                    window.location.href = '{{route('auth.login')}}';
                                }
                        });
                    });
                });
                </script>
            @endif

            <script>
                $(function () {
                    $('[data-toggle="tooltip"]').tooltip();
                })
            </script>
        @show
    </body>
</html>
