<?php


use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;

use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Teams\AgentController;
use App\Http\Controllers\Teams\DepartmentController;
use App\Http\Controllers\Teams\InviteController;
use App\Http\Controllers\User\UserController;

use App\Http\Controllers\Widget\WidgetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InitController;








Route::get('/widget/bootstrap/{license_id}', [WidgetController::class, 'bootstrap'])->middleware('valid-widget-bootstrap');

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, "register"]);
    Route::post('/login', [AuthController::class, "login"]);
});

/** Invite Code Validate */

Route::post('/teams/agent/invite/link/validate', [InviteController::class, 'validateLink']);
Route::post('/teams/agent/invite/accept', [InviteController::class, 'accept']);


Route::middleware(['auth:api', 'append-auth-response'])->group(function () {

    Route::middleware(['valid-user', 'valid-license', 'valid-agent'])->group(function () {
        Route::post('/initialize', action: [InitController::class, 'initialize']);

        Route::prefix('app')->group(function () {
            Route::post('/init', action: [InitController::class, 'appInit']);
            Route::post('/token', [InitController::class, 'appToken']);
        });

        Route::patch('settings', [SettingsController::class, 'update']);
        Route::prefix('teams')->group(function () {

            Route::resource('departments', DepartmentController::class)
                ->only(['store', 'update', 'destroy'])
                ->parameters(['departments' => 'id']);




            Route::prefix('agent')->group(function () {


                Route::patch('{id}', [AgentController::class, 'update']);
                Route::delete('{id}/delete', [AgentController::class, 'delete']);
                Route::post('{id}/suspend', [AgentController::class, 'suspend']);
                Route::prefix('invite')->group(function () {

                    Route::delete('{id}/cancel', [InviteController::class, 'cancel']);
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


