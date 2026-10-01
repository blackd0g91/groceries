<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">

        <title>@yield('title') · Groceries</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite([
            'resources/css/app.css',
            'resources/js/app.js'
        ])

    </head>

    <body class="min-h-dvh bg-paper font-sans text-ink antialiased">

        @if (session(\App\Http\Middleware\RequirePassword::SESSION_KEY))
            <x-header>@yield('toolbar')</x-header>
        @endif

        <main class="mx-auto max-w-xl px-4 pb-16 pt-6">
            @yield('content')
        </main>

    </body>

</html>
