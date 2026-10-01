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

        <div data-toast hidden aria-live="polite" class="pointer-events-none fixed inset-x-0 bottom-4 z-20 flex justify-center px-4">
            <div class="pointer-events-auto flex max-w-sm items-center gap-4 rounded-xl bg-ink py-2 pl-4 pr-2 text-sm text-paper shadow-lg">
                <span data-toast-message class="min-w-0 truncate"></span>
                <button type="button" data-toast-undo class="shrink-0 cursor-pointer rounded-lg px-3 py-1.5 font-semibold text-accent-soft transition-colors hover:bg-paper/10">Undo</button>
            </div>
        </div>

    </body>

</html>
