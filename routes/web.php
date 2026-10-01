<?php

use Illuminate\Support\Facades\Route;
use App\Models\Grocery;
use App\Models\Purchase;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('main');
});

Route::get('main', function() {
    return view('main');
});

Route::get('selected', function() {
    return view('selected');
});

Route::get('select/{id}', function($id) {
    
    $value = request()->query('value');
   
    $grocery = Grocery::find($id);

    if ($grocery) {
        $grocery->amount = $value;
        $grocery->save();
    }

    return redirect()->back();

});

Route::get('trash/{id}', function($id) {

    $grocery = Grocery::find($id);

    if ($grocery) {
        $grocery->amount = 0;
        $grocery->save();
    }

    return redirect()->back();

});

Route::get('trash-all', function() {

    $groceries = Grocery::all();

    foreach ($groceries as $grocery) {
        $grocery->amount = 0;
        $grocery->save();
    }

    return redirect('main');

});

Route::get('purchase', function() {

    $groceries = Grocery::all();

    foreach ($groceries as $grocery) {
        if ($grocery->amount > 0) {

            $purchase = new Purchase();
            $purchase->grocery_id = $grocery->id;
            $purchase->amount = $grocery->amount;
            $purchase->save();

            $grocery->amount = 0;
            $grocery->save();
        }
    }

    return redirect()->back();

});

Route::post('groceries/add', function() {

    $name = request()->input('name');

    if ($name) {
        $name = Str::title($name);

        $existingGrocery = Grocery::where('name', $name)->first();
        if ($existingGrocery) return redirect('main');

        $grocery = new Grocery();
        $grocery->name = $name;
        $grocery->amount = 0; // Default amount is 0
        $grocery->save();
    }

    return redirect('main');

});

