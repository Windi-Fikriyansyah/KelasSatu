<?php

use App\Http\Controllers\TusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use TusPhp\Tus\Server;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::any('/tus', [TusController::class, 'handle']);
Route::any('/tus/{any}', [TusController::class, 'handle'])->where('any', '.*');
