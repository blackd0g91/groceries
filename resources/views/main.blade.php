@php

use App\Models\Grocery;

$unselectedGroceries = Grocery::all()->where('amount', '=', 0)->sortBy('name');
$counterUnselected = 0;

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
                @php 
                    echo $grocery->tableRowUnselected($counterUnselected);
                    $counterUnselected++;
                @endphp
            @endforeach
        </tbody>
    </table>

    <form action="/groceries/add" method="POST" class="flex flex-col gap-2">
        @csrf
        <label for="name">Add Grocery</label>
        <div class="flex gap-2">
            <input style="width:100%" type="text" name="name" placeholder="Name...">
            <button class="btn-submit" type="submit">Add</button>
        </div>
    </form>

@endsection
