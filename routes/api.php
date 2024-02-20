<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\GuardianController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



    // ====================
    // == Authentication    ==
    // ====================

        Route::group([
            'middleware' => 'api',
            'prefix' => 'v1'
        ], function ($router) {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/user-profile', [AuthController::class, 'userProfile']);    
            Route::post('/update-account', [AuthController::class, 'updateProfile']);
            Route::post('/add-orphan', [AuthController::class, 'addOrphan']);
        });



    // ====================
    // ==    Guardian    ==
    // ====================

    // Route::group([
    //     'middleware' => 'api',
    //     'prefix' => 'v1'
    // ], function ($router) {
    //     Route::post('/complete-account', [GuardianController::class, 'accountCompletion']);
    //     Route::put('/guardians/{id}', [GuardianController::class, 'updateGuardian']);
    // });
    
