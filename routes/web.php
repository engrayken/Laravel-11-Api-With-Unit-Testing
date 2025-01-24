<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-exception', function () {
    throw new Exception("This is a test exception!");
});
