@extends('layouts.main')

@section('title', 'Login')

@section('content')

    <h1>Groceries</h1>

    <form action="{{ url('login') }}" method="POST" class="flex flex-col gap-2" style="max-width: 400px; margin: auto;">
        @csrf
        <label for="password">Password</label>
        <div class="flex gap-2">
            <input style="width:100%" type="password" id="password" name="password" required autofocus>
            <button class="btn-submit" type="submit">Enter</button>
        </div>
        @error('password')
            <span class="error">{{ $message }}</span>
        @enderror
    </form>

@endsection
