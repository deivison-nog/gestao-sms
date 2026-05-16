<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gestão SMS') }}</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/css/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        body { background: #f3f6fb; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #1b2a4a, #243b6d); }
        .sidebar .nav-link { color: rgba(255,255,255,.85); border-radius: .5rem; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { background: rgba(255,255,255,.14); color: #fff; }
        .card-modern { border: 0; border-radius: 1rem; box-shadow: 0 10px 25px rgba(20,38,80,.07); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        @auth
            <aside class="col-lg-2 col-md-3 sidebar p-3">
                <h5 class="text-white mb-4">Gestão SMS</h5>
                <nav class="nav flex-column gap-1">
                    @foreach($sidebarMenu as $menuItem)
                        <a class="nav-link {{ request()->routeIs($menuItem['route']) ? 'active' : '' }}" href="{{ route($menuItem['route']) }}">
                            {{ $menuItem['label'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>
        @endauth

        <main class="@auth col-lg-10 col-md-9 @else col-12 @endauth p-0">
            <nav class="navbar navbar-expand-lg bg-white shadow-sm px-4">
                <span class="navbar-brand mb-0 h1">{{ config('app.name', 'Gestão SMS') }}</span>
                <div class="ms-auto d-flex align-items-center gap-3">
                    @auth
                        <span class="text-muted">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn btn-outline-danger btn-sm">Sair</button></form>
                    @endauth
                </div>
            </nav>

            <div class="p-4">
                @include('partials.flash')
                @yield('content')
            </div>
        </main>
    </div>
</div>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
