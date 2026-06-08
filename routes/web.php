<?php

use Illuminate\Support\Facades\Route;
require __DIR__.'/../routes/patients.php';

Route::get('/', function () {
    return view('welcome');
});
