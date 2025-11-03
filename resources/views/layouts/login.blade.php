<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#10b981">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Meus Produtos">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="msapplication-TileColor" content="#10b981">
        <meta name="msapplication-TileImage" content="{{ asset('images/icon-192x192.png') }}">
        
        <!-- Favicon e Apple Touch Icons -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="96x96" href="{{ asset('images/icon-96x96.png') }}">
        <link rel="apple-touch-icon" sizes="128x128" href="{{ asset('images/icon-128x128.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/icon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="384x384" href="{{ asset('images/icon-384x384.png') }}">
        <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('images/icon-512x512.png') }}">
        
        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Login')</title>
        
        <!-- Bootstrap 5.3 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        
        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" id="main-css">
        
        @yield('styles')
        
    <!-- Custom JavaScript -->
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
    </style>
    </head>
<body>
    @yield('content')
    
    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>

