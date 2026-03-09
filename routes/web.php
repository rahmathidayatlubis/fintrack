<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Accounts
    Route::resource('accounts', AccountController::class);
    Route::post('/accounts/order', [AccountController::class, 'updateOrder'])->name('accounts.order');

    // Transactions
    Route::resource('transactions', TransactionController::class);
});

use App\Http\Controllers\DebtController;

Route::prefix('debts')->name('debts.')->middleware('auth')->group(function () {
    Route::get('/', [DebtController::class, 'index'])->name('index');
    Route::get('/{debt}', [DebtController::class, 'show'])->name('show');
    Route::get('/{debt}/edit', [DebtController::class, 'edit'])->name('edit');
    Route::put('/{debt}', [DebtController::class, 'update'])->name('update');
    Route::delete('/{debt}', [DebtController::class, 'destroy'])->name('destroy');
    Route::post('/{debt}/pay', [DebtController::class, 'pay'])->name('pay');
    Route::post('/{debt}/adjust', [DebtController::class, 'adjust'])->name('adjust');
    Route::post('/{debt}/mark-paid', [DebtController::class, 'markPaid'])->name('markPaid');
    Route::delete('/payments/{payment}', [DebtController::class, 'deletePayment'])->name('deletePayment');
});
