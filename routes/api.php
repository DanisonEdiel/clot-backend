<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [Login::class, 'login']);
    Route::post('register', [Register::class, 'register']);
    Route::post('password-reset', [AccountController::class, 'resetPassword']);
});

Route::prefix('account')->middleware([
    'authenticate'
])->group(function () {
    Route::get('show/{id}', [AccountController::class, 'show']);
    Route::put('update', [AccountController::class, 'update']);
    Route::delete('delete/{id}', [AccountController::class, 'destroy']);
    Route::post('profile-photo-update', [AccountController::class, 'updateAccountPhoto']);
    Route::put('update-password', [AccountController::class, 'updatePasswordAccount']);
});


Route::prefix('admin')->group(function () {
    Route::post('login', [\App\Http\Controllers\admin\AdminController::class, 'login']);
    Route::prefix('system')
        ->middleware('adminAuth')
        ->group(function () {
            Route::get('dashboard', [\App\Http\Controllers\admin\AdminController::class, 'dashboard']);
            Route::get('company/{tenantId}', [\App\Http\Controllers\admin\AdminController::class, 'showCompany']);
            Route::post('change-plan', [\App\Http\Controllers\admin\AdminController::class, 'changePlan']);
            Route::post('add-user', [\App\Http\Controllers\admin\AdminController::class, 'addAdmin']);
        });
});
