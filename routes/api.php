<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Country\CountryController;
use App\Http\Controllers\KYC\KYCController;
use App\Http\Controllers\Loan\LoanApplicationController;
use App\Http\Controllers\Loan\LoanTypeController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// country and state
Route::get('/country', [CountryController::class, 'country']);
Route::get('/state', [CountryController::class, 'state']);

// just to check
Route::get('/app', [LoanApplicationController::class, 'index']);

Route::middleware(['auth:api'])->group(function () {
    
    // user
    Route::group(['prefix' => 'users'], function() {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'updateInfo']);
        Route::put('/{id}', [UserController::class, 'updatePassword']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/', [UserController::class, 'currentUser']);
    });

    // Know your customer verification
    Route::group(['prefix' => 'user/verify'], function() {
        Route::post('/', [KYCController::class, 'store']);
        Route::put('/{id}', [KYCController::class, 'update']);
        Route::get('/{id}', [KYCController::class, 'show']);
        Route::get('/', [KYCController::class, 'index']);
        Route::delete('/{id}', [KYCController::class, 'destroy']);
        Route::put('/admin/{id}', [KYCController::class, 'verifyUser']);
    });

    // loan types
    Route::group(['prefix' => 'loan-types'], function() {
        Route::get('/', [LoanTypeController::class, 'index']);
        Route::post('/', [LoanTypeController::class, 'store']);
        Route::put('/{id}', [LoanTypeController::class, 'update']);
        Route::get('/{id}', [LoanTypeController::class, 'show']);
        Route::delete('/{id}', [LoanTypeController::class, 'destroy']);
    });
});