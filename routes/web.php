<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;



Route::resource('products', ProductController::class);

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
