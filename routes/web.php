<?php

use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\BotController;
use Illuminate\Support\Facades\Route;

Route::get('/webhook', [BotController::class, 'handle']);

Route::get('/login', [LoginRegisterController::class,'login'])->name('login');
Route::post('/authenticate', [LoginRegisterController::class,'authenticate'])->name('authenticate');
Route::get('/dashboard', [LoginRegisterController::class,'dashboard'])->name('dashboard')->middleware('auth');
Route::post('/logout', [LoginRegisterController::class,'logout'])->name('logout');
