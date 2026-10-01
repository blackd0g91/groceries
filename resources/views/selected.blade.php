@php

use App\Models\Grocery;

$selectedGroceries = Grocery::where('selected', true)->orderBy('name')->get();

@endphp

@extends('layouts.main')
 
@section('title', 'Selected')
 
@section('content')

    <h1>Purchase List</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Trash</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($selectedGroceries as $grocery)
                <x-grocery-row-selected :grocery="$grocery" :position="$loop->index" />
            @endforeach
        </tbody>
    </table>

@endsection
