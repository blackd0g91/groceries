@php

use App\Models\Grocery;
use Illuminate\Support\Str;

$unselectedGroceries = Grocery::where('selected', false)->get()
    ->sortBy(fn ($grocery) => Str::lower(Str::ascii($grocery->name)))
    ->values();

$groups = $unselectedGroceries->groupBy(fn ($grocery) => Str::upper(Str::substr(Str::ascii($grocery->name), 0, 1)));

@endphp

@extends('layouts.main')
 
@section('title', 'All items')

@section('toolbar')
    <form action="{{ url('groceries/add') }}" method="POST" class="pb-3">
        @csrf
        <div class="flex gap-2">
            <label class="relative flex-1">
                <span class="sr-only">Search or add an item</span>
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-muted" />
                <input type="search" name="name" data-filter value="{{ old('name') }}"
                       placeholder="Search or add an item…" maxlength="255" required autocomplete="off"
                       class="w-full rounded-xl border border-line bg-surface py-2.5 pl-10 pr-3 text-base placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/25">
            </label>
            <button type="submit" class="inline-flex cursor-pointer items-center gap-1 rounded-xl bg-accent px-4 font-medium text-accent-ink transition hover:brightness-110 active:scale-[.98]">
                <x-icon name="plus" class="size-5" />
                Add
            </button>
        </div>
        @error('name')
            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
        @enderror
    </form>
@endsection
 
@section('content')

    <div class="mb-5 flex items-baseline justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">All items</h1>
        <p data-item-count class="text-sm text-muted">{{ $unselectedGroceries->count() }} {{ Str::plural('item', $unselectedGroceries->count()) }}</p>
    </div>

    @foreach ($groups as $letter => $groceries)
        <section data-group class="mb-5">
            <h2 class="mb-2 px-1 text-xs font-semibold uppercase tracking-wider text-muted">{{ $letter }}</h2>
            <ul class="divide-y divide-line overflow-hidden rounded-2xl border border-line bg-surface">
                @foreach ($groceries as $grocery)
                    <x-grocery-row-unselected :grocery="$grocery" />
                @endforeach
            </ul>
        </section>
    @endforeach

    <p data-no-match hidden class="rounded-2xl border border-dashed border-line px-6 py-10 text-center text-muted">
        Nothing matches “<span data-query class="font-medium text-ink"></span>”.<br>
        Press <span class="font-medium text-ink">Add</span> to create it.
    </p>

    <div data-empty @if ($unselectedGroceries->isNotEmpty()) hidden @endif class="rounded-2xl border border-dashed border-line px-6 py-14 text-center">
        <span class="mx-auto mb-4 grid size-12 place-items-center rounded-full bg-accent-soft text-accent">
            <x-icon name="check" class="size-6" />
        </span>
        <h2 class="font-semibold">Everything is on your list</h2>
        <p class="mt-1 text-sm text-muted">Add a new item above, or check your purchase list.</p>
    </div>

@endsection
