<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RequirePassword;
use App\Models\Grocery;
use App\Models\Purchase;
use Illuminate\Support\Str;

Route::get('login', function() {
    if (session(RequirePassword::SESSION_KEY)) return redirect('/');

    return view('login');
})->name('login');

Route::post('login', function() {

    $validated = request()->validate([
        'password' => ['required', 'string'],
    ]);

    $expected = (string) config('app.password');

    if ($expected === '' || ! hash_equals($expected, $validated['password'])) {
        return back()->withErrors(['password' => 'Wrong password.']);
    }

    request()->session()->regenerate();
    session([RequirePassword::SESSION_KEY => true]);

    return redirect()->intended('/');

})->middleware('throttle:5,1');

Route::middleware(RequirePassword::class)->group(function () {

    Route::post('logout', function() {
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('login');
    });

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

        $grocery->selected = true;
        $grocery->save();

        return redirect()->back();

    });

    Route::post('trash/{grocery}', function(Grocery $grocery) {

        $grocery->selected = false;
        $grocery->save();

        return redirect()->back();

    });

    Route::post('trash-all', function() {

        Grocery::where('selected', true)->update(['selected' => false]);

        return redirect('main');

    });

    Route::post('purchase', function() {

        DB::transaction(function () {
            $groceries = Grocery::where('selected', true)->lockForUpdate()->get();

            foreach ($groceries as $grocery) {
                Purchase::create(['grocery_id' => $grocery->id]);

                $grocery->selected = false;
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

        Grocery::firstOrCreate(['name' => $name], ['selected' => false]);

        return redirect('main');

    });

});
