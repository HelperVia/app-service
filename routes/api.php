<?php


use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Teams\AgentController;
use App\Http\Controllers\Teams\InviteController;
use App\Http\Controllers\User\UserController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InitController;



Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, "register"]);
    Route::post('/login', [AuthController::class, "login"]);
});

/** Invite Code Validate */

Route::post('/teams/agent/invite/link/validate', [InviteController::class, 'validateLink']);


Route::middleware(['auth:api', 'append-auth-response'])->group(function () {



    Route::middleware(['valid-user', 'valid-license'])->group(function () {
        Route::post('/initialize', action: [InitController::class, 'initialize']);

        Route::prefix('app')->group(function () {
            Route::post('/init', [InitController::class, 'appInit']);
        });

        Route::prefix('teams')->group(function () {
            Route::prefix('agent')->group(function () {
                Route::post('{id}/update', [AgentController::class, 'update']);
                Route::prefix('invite')->group(function () {
                    Route::post('/', [InviteController::class, 'create']);
                    Route::post('email/validate', [InviteController::class, 'validateEmail']);

                });

            });

        });
    });




    Route::prefix('user')->group(function () {
        Route::post('/companies', [UserController::class, 'companies']);
        Route::post('/setting/update', [UserController::class, 'updateSettings']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/email/verify', [VerificationController::class, 'verifyEmail']);

    });


});









