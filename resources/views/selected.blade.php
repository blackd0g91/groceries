@php

use App\Models\Grocery;
use Illuminate\Support\Str;

$selectedGroceries = Grocery::where('selected', true)->get()
    ->sortBy(fn ($grocery) => Str::lower(Str::ascii($grocery->name)))
    ->values();

@endphp

@extends('layouts.main')
 
@section('title', 'Purchase list')
 
@section('content')

    <div class="mb-5 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Purchase list</h1>
            <p data-item-count class="text-sm text-muted">{{ $selectedGroceries->count() }} {{ Str::plural('item', $selectedGroceries->count()) }}</p>
        </div>

        <form method="POST" action="{{ url('trash-all') }}" data-confirm="Clear the whole purchase list?" data-hide-when-empty @if ($selectedGroceries->isEmpty()) hidden @endif>
            @csrf
            <button type="submit" class="cursor-pointer rounded-lg px-3 py-1.5 text-sm font-medium text-danger transition-colors hover:bg-danger-soft">
                Clear list
            </button>
        </form>
    </div>

    <div data-hide-when-empty @if ($selectedGroceries->isEmpty()) hidden @endif>
        <ul class="divide-y divide-line overflow-hidden rounded-2xl border border-line bg-surface">
            @foreach ($selectedGroceries as $grocery)
                <x-grocery-row-selected :grocery="$grocery" />
            @endforeach
        </ul>
        <p class="mt-3 px-1 text-xs text-muted">Tap a name to search for it on {{ config('market.tauste.name') }}.</p>
    </div>

    <div data-empty @if ($selectedGroceries->isNotEmpty()) hidden @endif class="rounded-2xl border border-dashed border-line px-6 py-14 text-center">
        <span class="mx-auto mb-4 grid size-12 place-items-center rounded-full bg-accent-soft text-accent">
            <x-icon name="bag" class="size-6" />
        </span>
        <h2 class="font-semibold">Your list is empty</h2>
        <p class="mt-1 text-sm text-muted">Tap <span class="font-medium text-ink">Buy</span> on any item to add it here.</p>
        <a href="{{ url('/') }}" class="mt-5 inline-flex items-center rounded-xl bg-accent px-4 py-2 text-sm font-medium text-accent-ink transition hover:brightness-110">
            Browse items
        </a>
    </div>

@endsection
