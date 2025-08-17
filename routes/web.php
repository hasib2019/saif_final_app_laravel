<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Define a login route for web authentication redirects
Route::get('/login', function () {
    return redirect('/api/login');
})->name('login');
