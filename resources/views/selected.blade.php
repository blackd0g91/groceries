@php

use App\Models\Grocery;

$selectedGroceries = Grocery::all()->where('amount', '>', 0)->sortBy('name');
$counter = 0;

@endphp

@extends('layouts.main')
 
@section('title', 'Selected')
 
@section('content')

    <h1>Purchase List</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th colspan="5">Amount</th>
                <th>Trash</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($selectedGroceries as $grocery)
                @php 
                    echo $grocery->tableRowSelected($counter);
                    $counter++;
                @endphp
            @endforeach
        </tbody>
    </table>

@endsection