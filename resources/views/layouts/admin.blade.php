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

            /* 5. Box / Panel (Modern Dark) */
            .box {
                background: #1a1a20 !important;
                border-top: 3px solid #6c5ce7 !important; /* Border atas Ungu */
                border-radius: 8px !important;
                color: #ffffff !important;
                box-shadow: 0 5px 15px rgba(0,0,0,0.5) !important;
            }
            .box-header {
                color: #ffffff !important;
                font-weight: 700 !important;
            }

            /* 6. Tombol & Badge (Tebal) */
            .btn {
                font-weight: 700 !important;
                border-radius: 5px !important;
                text-transform: uppercase;
                font-size: 11px;
            }
            .btn-primary {
                background-color: #6c5ce7 !important;
                border-color: #6c5ce7 !important;
            }
            .label {
                font-weight: 700 !important;
            }

            /* Footer */
            .main-footer {
                background: #0a0a0c !important;
                border-top: 1px solid #1a1a20 !important;
                color: #555 !important;
            }
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
