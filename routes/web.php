<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/dashboard-test', 'dashboard-test');

require __DIR__.'/settings.php';
require __DIR__.'/recruitment.php';
