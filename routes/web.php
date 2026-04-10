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
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

// ── Root redirect ─────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Guest ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Email Verification ────────────────────────────────────────────────────────
Route::get('/email/verify',               [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}',   [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');

// ── Auth only (no email verification required) ────────────────────────────────
Route::middleware('auth.custom')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ── Auth + Verified ───────────────────────────────────────────────────────────
Route::middleware('auth.custom')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Members ───────────────────────────────────────────────────────────────
    Route::middleware('role:System Administrator,Supreme Admin,Supreme Officer,Club Adviser,Org Admin,Org Officer')
        ->prefix('members')
        ->name('members.')
        ->group(function () {
            Route::get('/',                           [MemberController::class, 'index'])->name('index');
            Route::get('/create',                     [MemberController::class, 'create'])->name('create');
            Route::post('/',                          [MemberController::class, 'store'])->name('store');
            Route::get('/check-email',                [MemberController::class, 'checkEmail'])->name('check-email');
            Route::get('/{id}',                       [MemberController::class, 'show'])->name('show');
            Route::get('/{id}/edit',                  [MemberController::class, 'edit'])->name('edit');
            Route::put('/{id}',                       [MemberController::class, 'update'])->name('update');
            Route::delete('/{id}',                    [MemberController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/edit-history',          [MemberController::class, 'editHistory'])->name('edit-history');
            Route::get('/{id}/position-history-data', [MemberController::class, 'getPositionHistoryData'])->name('position-history-data');
            Route::post('/{id}/deactivate',           [MemberController::class, 'deactivate'])->name('deactivate');
            Route::post('/{id}/activate',             [MemberController::class, 'activate'])->name('activate');
        });

    // ── Documents ─────────────────────────────────────────────────────────────
    Route::middleware('role:System Administrator,Supreme Admin,Supreme Officer,Club Adviser,Org Admin,Org Officer')
        ->group(function () {
            Route::resource('documents', DocumentController::class);
            Route::get('documents/{document}/download',  [DocumentController::class, 'download'])->name('documents.download');
            Route::get('documents-trash',                [DocumentController::class, 'trash'])->name('documents.trash');
            Route::patch('documents-trash/{id}/restore', [DocumentController::class, 'restore'])->name('documents.restore');
            Route::delete('documents-trash/{id}/force',  [DocumentController::class, 'forceDelete'])->name('documents.force-delete');
        });

    // ── Budgets ───────────────────────────────────────────────────────────────
    Route::middleware('role:System Administrator,Supreme Admin,Supreme Officer,Club Adviser,Org Admin,Org Officer')
        ->prefix('budgets')
        ->name('budgets.')
        ->group(function () {
            Route::get('/',                   [BudgetController::class, 'index'])->name('index');
            Route::get('/create',             [BudgetController::class, 'create'])->name('create');
            Route::post('/',                  [BudgetController::class, 'store'])->name('store');
            Route::get('/export',             [BudgetController::class, 'export'])->name('export');
            Route::get('/copy-data/{budget}', [BudgetController::class, 'copyData'])->name('copy-data');
            Route::get('/{budget}',           [BudgetController::class, 'show'])->name('show');
            Route::get('/{budget}/edit',      [BudgetController::class, 'edit'])->name('edit');
            Route::put('/{budget}',           [BudgetController::class, 'update'])->name('update');
            Route::get('/{budget}/review',    [BudgetController::class, 'review'])->name('review');
            Route::post('/{budget}/approve',  [BudgetController::class, 'approve'])->name('approve');
            Route::post('/{budget}/reject',   [BudgetController::class, 'reject'])->name('reject');
            Route::delete('/{budget}',        [BudgetController::class, 'destroy'])->name('destroy');
            Route::get('/{budget}/copy',      [BudgetController::class, 'copy'])->name('copy');
            Route::post('/{budget}/disburse', [BudgetController::class, 'disburse'])->name('disburse');
        });

    // ── Administration ────────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->group(function () {

        // ── Users ─────────────────────────────────────────────────────────────
        Route::middleware('role:System Administrator,Supreme Admin,Club Adviser')
            ->prefix('users')->name('users.')
            ->group(function () {
                Route::get('/',                        [AdminController::class, 'users'])->name('index');
                Route::get('/create',                  [AdminController::class, 'createUser'])->name('create');
                Route::post('/',                       [AdminController::class, 'storeUser'])->name('store');
                Route::get('/{id}/edit',               [AdminController::class, 'editUser'])->name('edit');
                Route::put('/{id}',                    [AdminController::class, 'updateUser'])->name('update');
                Route::delete('/{id}',                 [AdminController::class, 'destroyUser'])->name('destroy');
                Route::post('/{id}/reset-password',    [AdminController::class, 'resetPassword'])->name('reset-password');
                Route::post('/{id}/send-verification', [AdminController::class, 'sendVerificationEmail'])->name('send-verification');
                Route::post('/{id}/verify-manual',     [AdminController::class, 'verifyEmailManually'])->name('verify-manual');
                Route::post('/{id}/restore',           [AdminController::class, 'restoreUser'])->name('restore');
                Route::delete('/{id}/force-delete',    [AdminController::class, 'forceDeleteUser'])->name('force-delete');
            });

        // ── Organizations ─────────────────────────────────────────────────────
        // {organization} matches the type-hinted parameter in OrganizationController
        // for Eloquent route model binding to work correctly.
        Route::middleware('role:System Administrator,Supreme Admin,Club Adviser,Org Admin')
            ->prefix('organizations')->name('organizations.')
            ->group(function () {
                Route::get('/',                       [OrganizationController::class, 'index'])->name('index');
                Route::get('/create',                 [OrganizationController::class, 'create'])->name('create');
                Route::post('/',                      [OrganizationController::class, 'store'])->name('store');
                Route::get('/{organization}',         [OrganizationController::class, 'show'])->name('show');
                Route::get('/{organization}/edit',    [OrganizationController::class, 'edit'])->name('edit');
                Route::put('/{organization}',         [OrganizationController::class, 'update'])->name('update');
                Route::delete('/{organization}',      [OrganizationController::class, 'destroy'])->name('destroy');
                Route::post('/{organization}/toggle', [OrganizationController::class, 'toggleActive'])->name('toggle');
            });

        // ── Roles ─────────────────────────────────────────────────────────────
        Route::middleware('role:System Administrator')
            ->prefix('roles')->name('roles.')
            ->group(function () {
                Route::get('/',          [AdminController::class, 'roles'])->name('index');
                Route::get('/create',    [AdminController::class, 'createRole'])->name('create');
                Route::post('/',         [AdminController::class, 'storeRole'])->name('store');
                Route::get('/{id}/edit', [AdminController::class, 'editRole'])->name('edit');
                Route::put('/{id}',      [AdminController::class, 'updateRole'])->name('update');
                Route::delete('/{id}',   [AdminController::class, 'destroyRole'])->name('destroy');
            });

        // ── Permissions ───────────────────────────────────────────────────────
        // POST added alongside PUT so Alpine's fetch() with _method=PUT spoofing
        // is matched by the router (browser fetch does not follow method override
        // unless the POST route exists).
        Route::middleware('role:System Administrator')
            ->prefix('permissions')->name('permissions.')
            ->group(function () {
                Route::get('/',        [PermissionController::class, 'index'])->name('index');
                Route::put('/{role}',  [PermissionController::class, 'update'])->name('update');
                Route::post('/{role}', [PermissionController::class, 'update']); // method-spoofing support
            });

        // ── Settings theme (POST under admin prefix) ──────────────────────────
        Route::post('/settings/theme', [SettingsController::class, 'updateTheme'])
            ->name('settings.theme.update')
            ->middleware('role:System Administrator,Supreme Admin,Club Adviser');
    });

    // ── Settings (GET — outside admin prefix to keep URL as /settings) ────────
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index')
        ->middleware('role:System Administrator,Supreme Admin,Club Adviser');

    // ── Audit Logs ────────────────────────────────────────────────────────────
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit.logs')
        ->middleware('role:System Administrator,Supreme Admin');

    // ── Profile ───────────────────────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',         [ProfileController::class, 'index'])->name('index');
        Route::put('/',         [ProfileController::class, 'updateProfile'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/theme',   [ProfileController::class, 'updateTheme'])->name('theme');
    });
});

// ── Flash message clear ───────────────────────────────────────────────────────
Route::post('/clear-flash-messages', function () {
    session()->forget(['success', 'error', 'warning', 'info', '_flash']);
    return response()->json(['success' => true]);
})->name('clear-flash');

// ── Password Reset ────────────────────────────────────────────────────────────
Route::get('/password/reset', fn() => view('auth.passwords.email'))->name('password.request');

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
        'email' => $request->query('email', ''),
    ]);
})->name('password.reset');

Route::post('/password/reset', function (Request $request) {
    $request->validate([
        'token'    => 'required',
        'email'    => 'required|email',
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