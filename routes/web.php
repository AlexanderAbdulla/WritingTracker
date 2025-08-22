<?php

use Illuminate\Support\Facades\Route;
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