@php

use App\Models\Grocery;

$unselectedGroceries = Grocery::where('amount', 0)->orderBy('name')->get();

@endphp

@extends('layouts.main')
 
@section('title', 'Main List')
 
@section('content')

    <h1>Groceries List</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th colspan="5">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($unselectedGroceries as $grocery)
                <x-grocery-row-unselected :grocery="$grocery" :position="$loop->index" />
            @endforeach
        </tbody>
    </table>

    <form action="/groceries/add" method="POST" class="flex flex-col gap-2">
        @csrf
        <label for="name">Add Grocery</label>
        <div class="flex gap-2">
            <input style="width:100%" type="text" id="name" name="name" placeholder="Name..." maxlength="255" required value="{{ old('name') }}">
            <button class="btn-submit" type="submit">Add</button>
        </div>
        @error('name')
            <span class="error">{{ $message }}</span>
        @enderror
    </form>

@endsection
