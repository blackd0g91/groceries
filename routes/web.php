<?php

use Illuminate\Support\Facades\DB;
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

Route::post('select/{grocery}', function(Grocery $grocery) {

    $validated = request()->validate([
        'value' => ['required', 'integer', 'between:1,5'],
    ]);

    $grocery->amount = (int) $validated['value'];
    $grocery->save();

    return redirect()->back();

});

Route::post('trash/{grocery}', function(Grocery $grocery) {

    $grocery->amount = 0;
    $grocery->save();

    return redirect()->back();

});

Route::post('trash-all', function() {

    Grocery::where('amount', '>', 0)->update(['amount' => 0]);

    return redirect('main');

});

Route::post('purchase', function() {

    DB::transaction(function () {
        $groceries = Grocery::where('amount', '>', 0)->lockForUpdate()->get();

        foreach ($groceries as $grocery) {
            Purchase::create([
                'grocery_id' => $grocery->id,
                'amount' => $grocery->amount,
            ]);

            $grocery->amount = 0;
            $grocery->save();
        }
    });

    return redirect()->back();

});

Route::post('groceries/add', function() {

    $validated = request()->validate([
        'name' => ['required', 'string', 'max:255'],
    ]);

    $name = Str::title(Str::squish($validated['name']));

    Grocery::firstOrCreate(['name' => $name], ['amount' => 0]);

    return redirect('main');

});
