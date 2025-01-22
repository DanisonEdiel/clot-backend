<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\Bot\BotController;
use App\Http\Controllers\Bot\BotLoginController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\CreateNewRuc;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Plans\PlanController;
use App\Http\Controllers\RucController;
use App\Http\Controllers\SelectTenant;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\SuscriptionUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [Login::class, 'login']);
    Route::post('register', [Register::class, 'register']);
    Route::post('password-reset', [AccountController::class, 'resetPassword']);
    Route::post('register-ruc', [CreateNewRuc::class, 'createNewRuc']);
    Route::post('bot/login', [BotLoginController::class, 'botLogin']);
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

Route::prefix('subscription')->middleware([
    'authenticate'

])->group(function () {
    Route::post('show', [SubscriptionController::class, 'show']);
    Route::post('update', [SubscriptionController::class, 'update']);
});

Route::prefix('config')->middleware([
    'authenticate'
])->group(function () {
    Route::get('show', [ConfigController::class, 'show']);
    Route::put('update', [ConfigController::class, 'update']);
});

Route::get('dashboard', [DashboardController::class, 'getTenantInfo'])->middleware(
    'authenticate'
);

Route::post('ruc-select', [SelectTenant::class, 'selectRuc'])->middleware(
    'authenticate'
);

Route::prefix('users')->middleware([
    'authenticate'
])->group(function () {
    Route::get('show', [UserController::class, 'show']);
    Route::put('update', [UserController::class, 'update']);
    Route::delete('delete/{id}', [UserController::class, 'destroy']);
    Route::post('add', [UserController::class, 'store']);
    Route::get('index', [UserController::class, 'index']);
});

Route::prefix('ruc')->middleware([
    'authenticate'
])->group(function () {
    Route::put('update', [RucController::class, 'update']);
    Route::post('synchronize', [BotController::class, 'sendTenantRucsToQueue']);
    Route::delete('delete/{id}', [RucController::class, 'destroy']);
    Route::post('add', [RucController::class, 'store']);
    Route::get('index', [RucController::class, 'index']);
});
Route::post('ruc/webhook', [RucController::class, 'webhook']);

Route::prefix('invoice')
// ->middleware(['authenticate'])
    ->group(function () {
        Route::get('index/{ruc}', [InvoiceController::class, 'index']);
        Route::post('custome-date', [BotController::class, 'sendRucToQueue']);
        Route::post('export-invoices', [InvoiceController::class, 'export']);
        Route::put('download/{id}', [InvoiceController::class, 'downloadSingle']);
        Route::put('download-invoices/{ruc}', [InvoiceController::class, 'downloadRange']);
    });

Route::prefix('credit-note')
    ->middleware([
        'authenticate'
    ])
    ->group(function () {
        Route::get('index/{ruc}', [\App\Http\Controllers\CreditNoteController::class, 'index']);
        Route::put('download/{id}', [\App\Http\Controllers\CreditNoteController::class, 'downloadSingle']);
        Route::put('download-credit-notes/{ruc}', [\App\Http\Controllers\CreditNoteController::class, 'downloadRange']);
    });

Route::prefix('debit-note')
    ->middleware([
        'authenticate'
    ])
    ->group(function () {
        Route::get('index/{ruc}', [\App\Http\Controllers\DebitNoteController::class, 'index']);
        Route::put('download/{id}', [\App\Http\Controllers\DebitNoteController::class, 'downloadSingle']);
        Route::put('download-debit-note/{ruc}', [\App\Http\Controllers\DebitNoteController::class, 'downloadRange']);
    });

Route::prefix('retention')->middleware(['authenticate'])
    ->group(function () {
        Route::get('index/{ruc}', [\App\Http\Controllers\RetentionController::class, 'index']);
        Route::put('download/{id}', [\App\Http\Controllers\RetentionController::class, 'downloadSingle']);
        Route::put('download-retentions/{ruc}', [\App\Http\Controllers\RetentionController::class, 'downloadRange']);
    });

Route::prefix('bot')
//    ->middleware(['authenticate'])
    ->group(function () {
        Route::post('add/invoice', [InvoiceController::class, 'store']);
        Route::post('add/credit-note', [\App\Http\Controllers\CreditNoteController::class, 'store']);
        Route::post('add/debit-note', [\App\Http\Controllers\DebitNoteController::class, 'store']);
        Route::post('add/retention', [\App\Http\Controllers\RetentionController::class, 'store']);
        Route::put('invalid-password/{id}', [BotController::class, 'wrongPassword']);
        Route::put('set-sync/{id}', [BotController::class, 'setSync']);
        Route::post('send-email', [BotController::class, 'sendEmail']);
    });

Route::prefix('plans')->middleware([
    'authenticate'
])->group(function () {
    Route::get('index', [PlanController::class, 'index']);
    Route::get('{id}', [PlanController::class, 'show']);
});


Route::prefix('payment')->middleware(['authenticate'])->group(function () {
    Route::post('pay', [PaymentController::class, 'findPlan']);
    Route::post('add', [PaymentController::class, 'addPayment']);
});

Route::prefix('annulled-vouchers')
//    ->middleware('authenticate')
    ->group(function () {
        Route::get('{ruc}', [\App\Http\Controllers\AnnulledVoucherController::class, 'index']);
        Route::post('check', [\App\Http\Controllers\AnnulledVoucherController::class, 'show']);

    });
Route::get('suscription', [SuscriptionUserController::class, 'getRucsInfo'])->middleware(
    'authenticate'
);

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
    Route::prefix('plan')
        ->middleware('adminAuth')
        ->group(function () {
            Route::get('index', [PlanController::class, 'index']);
            Route::get('{id}', [PlanController::class, 'show']);
            Route::post('add', [PlanController::class, 'store']);
            Route::put('update/{id}', [PlanController::class, 'update']);
            Route::delete('delete/{id}', [PlanController::class, 'destroy']);
        });
});
