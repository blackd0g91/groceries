@extends('layouts.main')

@section('title', 'Log in')

@section('content')

    <div class="flex min-h-[80dvh] items-center justify-center">
        <form action="{{ url('login') }}" method="POST" class="w-full max-w-sm rounded-3xl border border-line bg-surface p-8 shadow-sm">
            @csrf

            <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-accent text-accent-ink">
                <x-icon name="bag" class="size-6" />
            </span>
            <h1 class="mt-4 text-center text-xl font-semibold tracking-tight">Groceries</h1>
            <p class="mt-1 text-center text-sm text-muted">Enter the password to continue.</p>

            <label for="password" class="mt-6 block text-sm font-medium">Password</label>
            <div class="relative mt-1.5">
                <x-icon name="lock" class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-muted" />
                <input type="password" id="password" name="password" required autofocus autocomplete="current-password"
                       class="w-full rounded-xl border border-line bg-paper py-2.5 pl-10 pr-3 text-base focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/25 @error('password') border-danger @enderror">
            </div>
            @error('password')
                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
            @enderror

            <button type="submit" class="mt-5 w-full cursor-pointer rounded-xl bg-accent py-2.5 font-medium text-accent-ink transition hover:brightness-110 active:scale-[.99]">
                Continue
            </button>
        </form>
    </div>

@endsection
