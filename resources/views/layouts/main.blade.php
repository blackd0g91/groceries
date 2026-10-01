<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }} - @yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite([
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/css/tables.css'
        ])

    </head>

    <body class="bg-[#FFF] text-[#000]" style="padding: 20px;">
 
        @if (session(\App\Http\Middleware\RequirePassword::SESSION_KEY))
            <x-header />
        @endif
        
        @yield('content')

    </body>

</html>