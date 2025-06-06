<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/employees', [EmployeeController::class, 'index']);

Route::get('/me', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
