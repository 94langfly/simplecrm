<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Middleware\AuthApiMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware([AuthApiMiddleware::class])->group(function () {
    
    Route::prefix('company')->group(function () {
        Route::post('/store', [CompanyController::class, 'store']);
    });
    
    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeController::class, 'paginate']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::put('/{id}', [EmployeeController::class, 'update']);
        Route::get('/{id}', [EmployeeController::class, 'get']);
        Route::delete('/{id}', [EmployeeController::class, 'delete']);
    });
    
    Route::prefix('profile')->group(function () {
        Route::get('/', [EmployeeController::class, 'get']);
        Route::put('/', [EmployeeController::class, 'update']);
    });
    
});
