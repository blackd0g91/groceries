@extends('layouts.main')

@section('title', 'Edit item')

@section('content')

    <a href="{{ url('/') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-muted transition-colors hover:text-ink">
        <x-icon name="back" class="size-4" />
        All items
    </a>

    <h1 class="mb-5 text-2xl font-semibold tracking-tight">Edit item</h1>

    <form action="{{ url('groceries/' . $grocery->id) }}" method="POST" class="rounded-2xl border border-line bg-surface p-5">
        @csrf
        @method('PATCH')

        <label for="name" class="block text-sm font-medium">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $grocery->name) }}" maxlength="255" required autocomplete="off"
               class="mt-1.5 w-full rounded-xl border border-line bg-paper px-3 py-2.5 text-base focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/25 @error('name') border-danger @enderror">
        @error('name')
            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
        @enderror

        <button type="submit" class="mt-4 w-full cursor-pointer rounded-xl bg-accent py-2.5 font-medium text-accent-ink transition hover:brightness-110 active:scale-[.99]">
            Save
        </button>
    </form>

    <form action="{{ url('groceries/' . $grocery->id) }}" method="POST" data-confirm="Delete “{{ $grocery->name }}” for good?" class="mt-6 text-center">
        @csrf
        @method('DELETE')
        <button type="submit" class="cursor-pointer rounded-lg px-3 py-1.5 text-sm font-medium text-danger transition-colors hover:bg-danger-soft">
            Delete item
        </button>
    </form>

@endsection
