<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; 
use Illuminate\Support\Carbon;         // or: use Carbon\Carbon;
use App\Models\WritingSession;

Route::get('/', function () {
    $start = today()->subDays(6);      // last 7 days incl. today
    $end   = now()->endOfDay();

    // Guest? show Breeze's prebuilt login page
    if (auth()->guest()) {
        // Option A: render the login view at "/"
        return view('auth.login');

        // Option B (if you want the URL to be /login instead):
        // return to_route('login');
    }
    
    // base query (filter by user if logged in)
    $base = WritingSession::query();
    if (auth()->check()) {
        $base->where('user_id', auth()->id());
    }

    // roll up by day: words + minutes
    $rollup = (clone $base)
        ->whereBetween('time_finished', [$start, $end])
        ->selectRaw('DATE(time_finished) AS day, SUM(wordcount) AS words, SUM(minutes_spent) AS minutes')
        ->groupByRaw('DATE(time_finished)')
        ->orderBy('day')
        ->get()
        ->keyBy('day'); // map day => row

    // build exactly 7 rows (fill gaps with zeros) to match your Blade plucks
    $daily = collect();
    for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
        $key = $d->toDateString(); // 'YYYY-MM-DD'
        $row = $rollup->get($key);
        $daily->push((object) [
            'day'     => $key,
            'words'   => (int) ($row->words   ?? 0),
            'minutes' => (int) ($row->minutes ?? 0),
        ]);
    }

    // keep your table data if you need it
    $writingSessions = $base->latest('time_finished')->get();

    return view('welcome', compact('daily', 'writingSessions'));
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
        'time_finished'  => 'required|date'    
    ]);

    WritingSession::create($validated + ['user_id' => auth()->id()]);

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
