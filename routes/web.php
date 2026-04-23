<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});
    Route::get('/candidates', [CandidateController::class, 'index']);
    Route::post('/candidates/store', [CandidateController::class, 'store']);
    Route::get('/candidates/edit/{id}', [CandidateController::class, 'edit']);
    Route::post('/candidates/update/{id}', [CandidateController::class, 'update']);
    Route::get('/candidates/delete/{id}', [CandidateController::class, 'destroy']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
