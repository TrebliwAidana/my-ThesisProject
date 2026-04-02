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
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes — VSULHS_SSLG
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// ── Guest ─────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Email Verification (outside auth so the link works before login) ───────
Route::get('/email/verify',              [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}',  [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-resend',[EmailVerificationController::class, 'resend'])->name('verification.resend');

// ── Auth only (no email verification required) ─────────────────────────────
Route::middleware('auth.custom')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ── Auth + Verified ────────────────────────────────────────────────────────
Route::middleware(['auth.custom', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Members ───────────────────────────────────────────────────────────
    Route::middleware('role:System Administrator,Club Adviser,Org Admin,Org Officer')
        ->prefix('members')
        ->name('members.')
        ->group(function () {
            Route::get('/',                          [MemberController::class, 'index'])->name('index');
            Route::get('/create',                    [MemberController::class, 'create'])->name('create');
            Route::post('/',                         [MemberController::class, 'store'])->name('store');
            Route::get('/{id}',                      [MemberController::class, 'show'])->name('show');
            Route::get('/{id}/edit',                 [MemberController::class, 'edit'])->name('edit');
            Route::put('/{id}',                      [MemberController::class, 'update'])->name('update');
            Route::delete('/{id}',                   [MemberController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/edit-history',         [MemberController::class, 'editHistory'])->name('edit-history');
            Route::get('/{id}/position-history-data',[MemberController::class, 'getPositionHistoryData'])->name('position-history-data');
            Route::post('/{id}/deactivate',          [MemberController::class, 'deactivate'])->name('deactivate');
            Route::post('/{id}/activate',            [MemberController::class, 'activate'])->name('activate');
        });

    // ── Documents ─────────────────────────────────────────────────────────
    Route::middleware('role:System Administrator,Club Adviser,Org Admin,Org Officer')
        ->prefix('documents')
        ->name('documents.')
        ->group(function () {
            Route::get('/',         [DocumentController::class, 'index'])->name('index');
            Route::get('/upload',   [DocumentController::class, 'create'])->name('create');
            Route::post('/',        [DocumentController::class, 'store'])->name('store');
            Route::get('/{id}',     [DocumentController::class, 'show'])->name('show');
            Route::delete('/{id}',  [DocumentController::class, 'destroy'])->name('destroy');
        });

    // ── Budgets ───────────────────────────────────────────────────────────
    Route::middleware('role:System Administrator,Club Adviser,Org Admin,Org Officer')
        ->prefix('budgets')
        ->name('budgets.')
        ->group(function () {
            Route::get('/',                  [BudgetController::class, 'index'])->name('index');
            Route::get('/create',            [BudgetController::class, 'create'])->name('create');
            Route::post('/',                 [BudgetController::class, 'store'])->name('store');
            Route::get('/{budget}',          [BudgetController::class, 'show'])->name('show');
            Route::get('/{budget}/edit',     [BudgetController::class, 'edit'])->name('edit');
            Route::put('/{budget}',          [BudgetController::class, 'update'])->name('update');
            Route::get('/{budget}/review',   [BudgetController::class, 'review'])->name('review');
            Route::post('/{budget}/approve', [BudgetController::class, 'approve'])->name('approve');
            Route::delete('/{budget}',       [BudgetController::class, 'destroy'])->name('destroy');

            // Additional features
            Route::get('/export',            [BudgetController::class, 'export'])->name('export');
            Route::get('/{budget}/copy',     [BudgetController::class, 'copy'])->name('copy');
            Route::post('/{budget}/disburse',[BudgetController::class, 'disburse'])->name('disburse');
            Route::get('/copy-data/{budget}',[BudgetController::class, 'copyData'])->name('copy-data');
        });

    // ── Admin ─────────────────────────────────────────────────────────────
    Route::middleware('role:System Administrator,Supreme Admin,Club Adviser')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Users
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/',                          [AdminController::class, 'users'])->name('index');
                Route::get('/create',                    [AdminController::class, 'createUser'])->name('create');
                Route::post('/',                         [AdminController::class, 'storeUser'])->name('store');
                Route::get('/{id}/edit',                 [AdminController::class, 'editUser'])->name('edit');
                Route::put('/{id}',                      [AdminController::class, 'updateUser'])->name('update');
                Route::delete('/{id}',                   [AdminController::class, 'destroyUser'])->name('destroy');
                Route::post('/{id}/reset-password',      [AdminController::class, 'resetPassword'])->name('reset-password');
                Route::post('/{id}/send-verification',   [AdminController::class, 'sendVerificationEmail'])->name('send-verification');
                Route::post('/{id}/verify-manual',       [AdminController::class, 'verifyEmailManually'])->name('verify-manual');
            });

            // Permissions
            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::get('/',       [AdminController::class, 'permissions'])->name('index');
                Route::post('/sync',  [AdminController::class, 'syncPermissions'])->name('sync');
            });
        });

    // ── Settings ──────────────────────────────────────────────────────────
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index')
        ->middleware('role:System Administrator,Supreme Admin,Club Adviser');

    Route::post('/admin/settings/theme', [SettingsController::class, 'updateTheme'])
        ->name('settings.theme.update')
        ->middleware('role:System Administrator,Supreme Admin,Club Adviser');

    // ── Audit Logs ────────────────────────────────────────────────────────
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit.logs')
        ->middleware('role:System Administrator,Supreme Admin');

    // ── Profile (all authenticated users) ─────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',           [ProfileController::class, 'index'])->name('index');
        Route::put('/',           [ProfileController::class, 'updateProfile'])->name('update');
        Route::put('/password',   [ProfileController::class, 'updatePassword'])->name('password');
    });
});

// ── Flash message clear ────────────────────────────────────────────────────
Route::post('/clear-flash-messages', function () {
    session()->forget(['success', 'error', 'warning', 'info', '_flash']);
    return response()->json(['success' => true]);
})->name('clear-flash');

// ── Password Reset Routes (available to guests) ───────────────────────────
Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::post('/password/email', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));
    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('/password/reset/{token}', function ($token, Request $request) {
    return view('auth.passwords.reset', [
        'token' => $token,
        'email' => $request->query('email', '')
    ]);
})->name('password.reset');

Route::post('/password/reset', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        }
    );
    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->name('password.update');

// ── Admin roles management (requires role ID 1) ───────────────────────────
Route::middleware(['auth', 'role:1'])->prefix('admin')->group(function () {
    Route::get('/roles',          [AdminController::class, 'roles'])->name('admin.roles.index');
    Route::get('/roles/create',   [AdminController::class, 'createRole'])->name('admin.roles.create');
    Route::post('/roles',         [AdminController::class, 'storeRole'])->name('admin.roles.store');
    Route::get('/roles/{id}/edit',[AdminController::class, 'editRole'])->name('admin.roles.edit');
    Route::put('/roles/{id}',     [AdminController::class, 'updateRole'])->name('admin.roles.update');
    Route::delete('/roles/{id}',  [AdminController::class, 'destroyRole'])->name('admin.roles.destroy');
});