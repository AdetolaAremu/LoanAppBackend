<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Country\CountryController;
use App\Http\Controllers\KYC\KYCController;
use App\Http\Controllers\Loan\LoanApplicationController;
use App\Http\Controllers\Loan\LoanTypeController;
use App\Http\Controllers\Statistics\StatisticsController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

// authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// country and state
Route::get('/countries', [CountryController::class, 'country']);
Route::get('/state/{id}', [CountryController::class, 'state']);

Route::group(["middleware" => "auth:api"], function(){
    // logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // user
    Route::group(['prefix' => 'users'], function() {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'updateInfo']);
        Route::put('/password/{id}', [UserController::class, 'updatePassword']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    Route::put('user/update', [UserController::class, 'updateInfo']);

    // get logged in user
    Route::get('user/currentuser', [UserController::class, 'currentUser']);

    // Know your customer verification
    Route::group(['prefix' => 'user/verify'], function() {
        Route::post('/', [KYCController::class, 'store']);
        Route::put('/{id}', [KYCController::class, 'update']);
        Route::get('/{id}', [KYCController::class, 'show']);
        Route::get('/', [KYCController::class, 'index']);
        Route::delete('/{id}', [KYCController::class, 'destroy']);
        Route::put('/approve/{id}', [KYCController::class, 'approveKYC']);
        Route::put('/reject/{id}', [KYCController::class, 'rejectKYC']);
        Route::put('/recycle/{id}', [KYCController::class, 'recycleKYC']);
    });

    // get logged in user KYC
    Route::get('/mykyc', [KYCController::class, 'getMyKYC']);

    // loan types
    Route::group(['prefix' => 'loan-types'], function() {
        Route::get('/', [LoanTypeController::class, 'index']);
        Route::post('/', [LoanTypeController::class, 'store']);
        Route::put('/{id}', [LoanTypeController::class, 'update']);
        Route::get('/{id}', [LoanTypeController::class, 'show']);
        Route::delete('/{id}', [LoanTypeController::class, 'destroy']);
    });

    // loan application
    Route::group(['prefix' => 'loan-application'], function() {
        Route::get('/', [LoanApplicationController::class, 'getUserLoan']);
        Route::post('/', [LoanApplicationController::class, 'store']);
        Route::get('/{id}', [LoanApplicationController::class, 'show']);
        Route::put('/approve/{id}', [LoanApplicationController::class, 'approveLoan']);
        Route::put('/reject/{id}', [LoanApplicationController::class, 'rejectLoan']);
        Route::put('/recycle/{id}', [LoanApplicationController::class, 'recycleLoan']);
        Route::delete('/{id}', [LoanApplicationController::class, 'destroy']);
        Route::get('/status', [LoanApplicationController::class, 'kycCheck']);
    });

    // Loan Application status table
    Route::get('/loan/{status}', [LoanApplicationController::class, 'getStatus']);

    // KYC status table
    Route::get('/kyc/{status}', [KYCController::class, 'getStatus']);

    // Count of KYC and Loan Application status i.e pending, successful, failed
    Route::get('/status-count', [StatisticsController::class, 'allStatus']); 
    // Route::get('/loan-count', [StatisticsController::class, 'loanCount']);

    // All dashboard statistics
    Route::get('/dashboard-count', [StatisticsController::class, 'dashboardCount']);
    Route::get('/latest-users', [StatisticsController::class, 'latestUsers']);

    Route::get('/test', [LoanApplicationController::class, 'pluckUserStatus']);
});