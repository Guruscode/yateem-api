<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\PasswordResetController;

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
            Route::put('/editProfile/{id}', [AuthController::class, 'editProfile']);
            // Route::post('/add-orphan', [AuthController::class, 'addOrphan']);
            // Route::put('/editOrphan/{id}', [AuthController::class, 'editOrphan']);
        });


        // Non authenticated Actions

    Route::group(['prefix' => 'v1' ], function () {
        Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
        Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
    });


        // Routes for Guardians
        Route::group([
            'middleware' => 'api',
            'prefix' => 'v1/guardian'
        ], function ($router) {
            Route::post('/update-account', [GuardianController::class, 'addProfile']);
            Route::post('/add-orphan', [GuardianController::class, 'addOrphan']);
            Route::put('/edit-orphan/{id}', [GuardianController::class, 'editOrphan']);
            Route::post('/sponsorship-request', [GuardianController::class, 'createSponsorshipRequest']);
            Route::post('/upload-document', [GuardianController::class, 'uploadDocument']);
        });

        // Routes for Sponsors
        Route::group([
            'middleware' => 'api',
            'prefix' => 'v1/sponsor'
        ], function ($router) {
            Route::get('/orphans', [SponsorController::class, 'viewAvailableOrphans']);
            Route::post('/sponsor', [SponsorController::class, 'sponsorOrphan']);
            Route::post('/make-payment', [SponsorController::class, 'makePayment']);
        });

        // Routes for Admin
        Route::group([
            'middleware' => 'api',
            'prefix' => 'v1/admin'
        ], function ($router) {
            Route::get('/pending-orphans', [AdminController::class, 'viewPendingOrphans']);
            Route::put('/approve-orphan/{id}', [AdminController::class, 'approveOrphan']);
            Route::get('/guardian-profiles', [AdminController::class, 'viewGuardianProfiles']);
            Route::put('/approve-sponsorship/{id}', [AdminController::class, 'approveSponsorship']);
        });