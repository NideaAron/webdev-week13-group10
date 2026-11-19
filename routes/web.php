<?php

use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/photos');
Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
Route::get('/photos/create', [PhotoController::class, 'create'])->name('photos.create');
Route::post('/photos', [PhotoController::class, 'store'])->name('photos.store');
Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');