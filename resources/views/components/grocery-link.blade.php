@props(['grocery'])

<a href="{{ $grocery->tausteSearchUrl() }}" target="_blank" rel="noopener noreferrer"
   title="Search on {{ config('market.tauste.name') }}"
   class="group flex min-w-0 flex-1 items-center gap-2 py-1 font-medium">
    <span class="truncate group-hover:underline group-hover:decoration-line group-hover:underline-offset-4">{{ $grocery->name }}</span>
    <x-icon name="external" class="size-3.5 shrink-0 text-muted opacity-40 transition-opacity group-hover:opacity-100" />
</a>
