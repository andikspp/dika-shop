<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuyerController;

Route::post('/register', [BuyerController::class, 'register'])->name('register');
