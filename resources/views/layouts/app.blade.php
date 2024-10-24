<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Traduttore Italiano - CAA per Bambini</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <header>
            <h1><a href="{{ url('/') }}">Traduttore Magico: Italiano - CAA</a></h1>
        </header>
        <div class="container">
            @yield('content')
        </div>
        
        @stack('scripts')
    </body>
</html>
