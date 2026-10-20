<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home');
Route::view('/legal', 'legal');

// Source page for the Open Graph image; only reachable during local development.
if (app()->isLocal()) {
    Route::view('/og-image', 'og-image');
}
