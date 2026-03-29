<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — VSULHS_SSLG
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated routes
Route::middleware('auth.custom')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Members — Adviser & Officer
    Route::middleware('role:Adviser,Officer')->prefix('members')->name('members.')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('index');
        Route::get('/create', [MemberController::class, 'create'])->name('create');
        Route::post('/', [MemberController::class, 'store'])->name('store');
        Route::get('/{id}', [MemberController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [MemberController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MemberController::class, 'update'])->name('update');
        Route::delete('/{id}', [MemberController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/position-history', [MemberController::class, 'positionHistory'])->name('position-history');
        Route::get('members/{id}/position-history-data', [MemberController::class, 'getPositionHistoryData'])->name('members.position-history-data');
    });

    // Documents — Adviser & Officer
    Route::middleware('role:Adviser,Officer')->prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('index');
        Route::get('/upload', [DocumentController::class, 'create'])->name('create');
        Route::post('/', [DocumentController::class, 'store'])->name('store');
        Route::get('/{id}', [DocumentController::class, 'show'])->name('show');
        Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy');
    });

    // Budgets — Adviser, Officer, Auditor
    Route::middleware('role:Adviser,Officer,Auditor')->prefix('budgets')->name('budgets.')->group(function () {
        Route::get('/', [BudgetController::class, 'index'])->name('index');
        Route::get('/create', [BudgetController::class, 'create'])->name('create');
        Route::post('/', [BudgetController::class, 'store'])->name('store');
        Route::get('/{budget}', [BudgetController::class, 'show'])->name('show');
        Route::get('/{budget}/edit', [BudgetController::class, 'edit'])->name('edit');
        Route::put('/{budget}', [BudgetController::class, 'update'])->name('update');
        Route::get('/{budget}/review', [BudgetController::class, 'review'])->name('review');
        Route::post('/{budget}/approve', [BudgetController::class, 'approve'])->name('approve');
        Route::delete('/{budget}', [BudgetController::class, 'destroy'])->name('destroy');
    });

    // Admin-only routes (Adviser only)
    Route::middleware('role:Adviser')->prefix('admin')->name('admin.')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::get('/create', [AdminController::class, 'createUser'])->name('create');
            Route::post('/', [AdminController::class, 'storeUser'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'editUser'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'destroyUser'])->name('destroy');
        });

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [AdminController::class, 'roles'])->name('index');
            Route::post('/', [AdminController::class, 'storeRole'])->name('store');
            Route::delete('/{id}', [AdminController::class, 'destroyRole'])->name('destroy');
        });

        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [AdminController::class, 'permissions'])->name('index');
            Route::post('/sync', [AdminController::class, 'syncPermissions'])->name('sync');
        });
    });

    // Settings — Adviser only
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index')
        ->middleware('role:Adviser');

    // Audit Logs — Adviser only
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit.logs')
        ->middleware('role:Adviser');

    // Theme update endpoint
    Route::post('/admin/settings/theme', [SettingsController::class, 'updateTheme'])
        ->name('settings.theme.update')
        ->middleware('role:Adviser');
    
    // Profile routes (for all authenticated users)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'updateProfile'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    
   
    


    
    });

   
    
});