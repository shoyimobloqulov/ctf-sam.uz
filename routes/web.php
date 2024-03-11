<?php

use App\Http\Controllers\BotController;
use Illuminate\Support\Facades\Route;

Route::get('/webhook', [BotController::class, 'handle']);
