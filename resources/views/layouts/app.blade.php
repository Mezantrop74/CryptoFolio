<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <link href="{{ asset('static/css/base.css') }}" rel="stylesheet">
    @if(auth()->user()->color_scheme == "light")
        <link href="{{ asset('static/css/light.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('static/css/dark.css') }}" rel="stylesheet">
    @endif
    @yield('styles')
    <link href="{{ asset('static/css/fonts.css') }}" rel="stylesheet">
</head>

<body @if(auth()->user()->color_scheme == "dark") data-theme="dark" @endif>
<div class="wrapper">
    <nav id="sidebar" class="sidebar js-sidebar">
        <div class="sidebar-content js-simplebar">
            <a class="sidebar-brand" href="{{ route('home') }}">
                <span class="align-middle">{{ __('Cryptocurrencies Portfolio') }}<sup class="text-muted">@version('compact')</sup></span>
            </a>

            <ul class="sidebar-nav">
                <li class="sidebar-item {{ Route::currentRouteName() == 'home' ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('home') }}">
                        <i class="align-middle" data-feather="sliders"></i> <span
                            class="align-middle">{{ __('Dashboard') }}</span>
                    </a>
                </li>

                <li class="sidebar-header">
                    {{ __('Assets') }}
                </li>
                <li class="sidebar-item {{ Route::currentRouteName() == 'crypto.index' ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('crypto.index') }}">
                        <i class="align-middle" data-feather="dollar-sign"></i> <span
                            class="align-middle">{{ __('Assets') }}</span>
                    </a>
                </li>

                <li class="sidebar-item {{ Route::currentRouteName() == 'crypto.exchange.index' ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('crypto.exchange.index') }}">
                        <i class="align-middle" data-feather="percent"></i> <span
                            class="align-middle">{{ __('Exchanges') }}</span>
                    </a>
                </li>

                <li class="sidebar-item {{ Route::currentRouteName() == 'reports.index' ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('reports.index') }}">
                        <i class="align-middle" data-feather="file-text"></i> <span
                            class="align-middle">{{ __('Reports') }}</span>
                    </a>
                </li>

                <li class="sidebar-header">
                    {{ __('Community') }}
                </li>
                <li class="sidebar-item {{ Route::currentRouteName() == 'newsfeed.index' ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('newsfeed.index') }}">
                        <i class="align-middle" data-feather="rss"></i> <span
                            class="align-middle">{{ __('News feed') }}</span>
                    </a>
                </li>

                @if(auth()->user()->is_admin)
                    <li class="sidebar-header">
                        {{ __('Admin menu') }}
                    </li>
                    <li class="sidebar-item {{ Route::currentRouteName() == 'admin.users.index' ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('admin.users.index') }}">
                            <i class="align-middle" data-feather="users"></i> <span
                                class="align-middle">{{ __('Users') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Route::currentRouteName() == 'admin.settings.index' ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('admin.settings.index') }}">
                            <i class="align-middle" data-feather="settings"></i> <span
                                class="align-middle">{{ __('Settings') }}</span>
                        </a>
                    </li>

                    <li class="sidebar-header">
                        {{ __('Misc') }}
                    </li>
                    <li class="sidebar-item {{ Route::currentRouteName() == 'bip-converter.index' ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('bip-converter.index') }}">
                            <i class="align-middle" data-feather="key"></i> <span
                                class="align-middle">{{ __('BIP-39 Converter') }}</span>
                        </a>
                    </li>
                @endif


            </ul>
        </div>
    </nav>

    <div class="main">
        <nav class="navbar navbar-expand navbar-light navbar-bg">
            <a class="sidebar-toggle js-sidebar-toggle">
                <i class="hamburger align-self-center"></i>
            </a>

            <div class="navbar-collapse collapse">
                <ul class="navbar-nav navbar-align">
                    <li class="nav-item">
                        <a id="currentTime" class="nav-link js-fullscreen d-none d-lg-block"
                           onclick="return false;"
                           href="#">
                            <span>{{ \Carbon\Carbon::now() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-icon js-fullscreen d-none d-lg-block"
                           onclick="event.preventDefault(); document.getElementById('toggleColorschemeForm').submit();"
                           href="#">
                            @if(auth()->user()->color_scheme == 'light')<i data-feather="moon"></i> @else <i
                                data-feather="sun"></i> @endif
                        </a>
                        <form action="{{ route('settings.view.colorscheme.toggle') }}" method="POST"
                              id="toggleColorschemeForm">
                            @csrf
                            <input type="hidden" name="toggle_colorscheme" value="true">
                        </form>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                            <span class="text-dark">{{ auth()->user()->login }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('settings.index') }}"><i class="align-middle me-1"
                                                                                             data-feather="settings"></i>
                                {{ __('Settings') }}</a>
                            <a class="dropdown-item" href=""
                               onclick="event.preventDefault(); document.getElementById('logout-top').submit();">
                                <i class="align-middle me-1" data-feather="log-out"></i>{{ __('Logout') }}
                            </a>
                            <form id="logout-top" action="{{ route('logout') }}" method="POST"
                                  style="display: none;">
                                @csrf
                            </form>
                        </div>

                    </li>
                </ul>
            </div>
        </nav>

        @yield('content')
        <footer class="footer">
            <div class="container-fluid">
                <div class="row text-muted">
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="{{ asset('static/js/jquery.js') }}"></script>
<script src="{{ asset('static/js/app.js') }}"></script>
<script src="{{ asset('static/js/luxon.min.js') }}"></script>
@stack('scripts')

<script>
    function dataRefreshed() {
        $('#currentTime').addClass('text-success')
        setTimeout(() => {
            $('#currentTime').removeClass('text-success')
        }, 1000);
    }

    $(() => {
        let dt = luxon.DateTime.fromISO('{{ \Carbon\Carbon::now()->toIso8601String() }}', {zone: "utc"});
        setInterval(() => {
            dt = dt.plus({seconds: 1})
            $('#currentTime').text(dt.toFormat('yyyy-LL-dd HH:mm:ss'));
        }, 1000)

        setInterval(() => {
            fetchTime((resp) => {
                dt = luxon.DateTime.fromISO(resp.time, {zone: "utc"})
            })
        }, 60000)

        function fetchTime(callback) {
            fetch('{{ route('api.app.time') }}?api_token={{ auth()->user()->api_token }}', {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            }).then(resp => resp.json()).then(resp => {
                callback(resp)
            })
        }
    })

</script>
</body>

</html>
