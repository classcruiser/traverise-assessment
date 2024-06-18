<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\SchedulerController;
use App\Http\Controllers\Panel\TenantController;
use App\Http\Controllers\AutomatedEmailController;
use App\Http\Controllers\Booking\PaypalController;
use App\Http\Controllers\Panel\DashboardController;

if (!defined('CONTROLLERS')) {
    define('CONTROLLERS', 'App\\Http\\Controllers\\');
}
if (!defined('PANEL_CONTROLLER')) {
    define('PANEL_CONTROLLER', CONTROLLERS . 'Panel\\');
}

/**
 * domain : auth
 */
Route::domain(env('AUTH_URI'))->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');
    Route::get('/', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/', [AuthController::class, 'attempt'])->name('auth.login.attempt');
    Route::get('verify-email/{string}', [AuthController::class, 'verify'])->name('auth.login.verify');
});

/**
 * domain : panel
 */
Route::domain(env('PANEL_URI'))->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('panel.dashboard');

    Route::group(['prefix' => 'tenants'], function () {
        Route::get('/', [TenantController::class, 'index'])->name('tenants');
        Route::get('new', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/', [TenantController::class, 'store'])->name('tenants.store');
        Route::get('{tenant}', [TenantController::class, 'show'])->name('tenants.show');
        Route::put('{tenant}', [TenantController::class, 'update'])->name('tenants.put');
        Route::patch('{id}/restore', [TenantController::class, 'restore'])->name('tenants.restore');
        Route::get('{tenant}/delete', [TenantController::class, 'delete'])->name('tenants.delete');
        Route::post('domain-checker', [TenantController::class, 'domainChecker'])->name('tenants.domain-checker');

        Route::get('{tenant}/stripe/connect', [StripeController::class, 'connect'])->name('tenants.connect-stripe');
        Route::get('{tenant}/stripe/delete-account', [StripeController::class, 'delete'])->name('tenants.delete-stripe');
        Route::get('{tenant}/stripe/onboarding', [StripeController::class, 'onboarding'])->name('tenants.onboarding-stripe');
    });
});

Route::domain(config('app.system_uri'))->group(function () {
    Route::get('/', function () {
        return view('homepage.index')->name('home');
    });

    Route::get('database', [DatabaseController::class, 'index'])->name('database.index');
    Route::post('database', [DatabaseController::class, 'restore'])->name('database.restore');

    Route::get('stripe-onboarding/{id}/{account_id}', [StripeController::class, 'startOnboarding'])->name('stripe.onboarding');
    Route::get('stripe-onboarding/{id}/{account_id}/finish', [StripeController::class, 'finishOnboarding'])->name('stripe.onboarding-finish');
    Route::get('automated-emails', [AutomatedEmailController::class, 'run'])->name('tenants.automated-emails');

    Route::get('maintenance', [SchedulerController::class, 'index'])->name('maintenance');
    Route::post('stripe-webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');
    Route::post('paypal-webhook', [PaypalController::class, 'webhook'])->name('paypal.webhook');
});
