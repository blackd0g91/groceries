@php

use App\Models\Grocery;

$selectedCount = Grocery::where('selected', true)->count();

$tabs = [
    ['label' => 'All items', 'href' => url('/'), 'active' => request()->is('/', 'main')],
    ['label' => 'Purchase list', 'href' => url('selected'), 'active' => request()->is('selected'), 'badge' => true],
];

@endphp

<header class="sticky top-0 z-10 border-b border-line bg-paper/85 backdrop-blur">
    <div class="mx-auto max-w-xl px-4">

        <div class="flex items-center justify-between py-3">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-lg font-semibold tracking-tight">
                <span class="grid size-9 place-items-center rounded-xl bg-accent text-accent-ink">
                    <x-icon name="bag" class="size-5" />
                </span>
                Groceries
            </a>

            <form method="POST" action="{{ url('logout') }}">
                @csrf
                <button type="submit" class="grid size-9 cursor-pointer place-items-center rounded-xl text-muted transition-colors hover:bg-line hover:text-ink" aria-label="Log out" title="Log out">
                    <x-icon name="logout" class="size-5" />
                </button>
            </form>
        </div>

        <nav class="mb-3 grid grid-cols-2 gap-1 rounded-xl bg-ink/[0.06] p-1 text-sm font-medium">
            @foreach ($tabs as $tab)
                <a href="{{ $tab['href'] }}"
                   @if ($tab['active']) aria-current="page" @endif
                   class="flex items-center justify-center gap-2 rounded-lg px-3 py-2 transition-colors {{ $tab['active'] ? 'bg-raised text-ink shadow-sm' : 'text-muted hover:text-ink' }}">
                    {{ $tab['label'] }}
                    @isset($tab['badge'])
                        <span data-selected-count @if ($selectedCount === 0) hidden @endif class="min-w-5 rounded-full bg-accent px-1.5 text-center text-xs leading-5 text-accent-ink">{{ $selectedCount }}</span>
                    @endisset
                </a>
            @endforeach
        </nav>

        {{ $slot }}

    </div>
</header>
