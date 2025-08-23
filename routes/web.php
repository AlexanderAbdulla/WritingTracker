<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; 
use App\Models\WritingSession;

Route::get('/', function () {
    $writingSessions = WritingSession::all(); // get all rows
    $daily = WritingSession::selectRaw('DATE(time_finished) as day, SUM(wordcount) as words, SUM(minutes_spent) as minutes')
        ->groupBy('day')
        ->orderBy('day')
        ->get();
    return view('welcome', compact('writingSessions', 'daily'));
});


Route::get('/whoami', function () {
    return auth()->check()
        ? 'Logged in as: ' . auth()->user()->email
        : 'Not logged in';
});

Route::post('/sessions', function (Request $request) {
    $validated = $request->validate([
        'project_name'   => 'required|string|max:255',
        'wordcount'      => 'required|integer',
        'minutes_spent'  => 'required|integer',
        'time_finished'  => 'required|date',
        'user_id'        => 'required|string|max:255',
    ]);

    WritingSession::create($validated);

    return redirect('/')->with('success', 'Writing session created!');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
