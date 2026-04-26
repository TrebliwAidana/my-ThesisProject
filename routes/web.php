<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentVersionController;
use App\Http\Controllers\Admin\DocumentCategoryController;
use App\Http\Controllers\Admin\DocumentBackupController;
use App\Http\Controllers\Admin\FinancialCategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\Financial\IncomeController;
use App\Http\Controllers\Financial\ExpenseController;
use App\Http\Controllers\Financial\ReceivableController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Role Access Matrix
|--------------------------------------------------------------------------
| Role                  | ID | Dashboard | Members | Financial | Documents | Admin | Profile |
| System Administrator  |  1 |     ✅    |    ✅   |     ✅    |     ✅    |   ✅  |    ✅   |
| Club Adviser          |  2 |     ✅    |    ✅   |     ✅    |     ✅    |   ❌  |    ✅   |
| Treasurer             |  3 |     ✅    |    ✅   |     ✅    |     ✅    |   ❌  |    ✅   |
| Auditor               |  4 |     ✅    |    ✅   |     ✅    |     ✅    |   ❌  |    ✅   |
| Guest                 |  5 |     ✅    |    ❌   |  ✅ r/o   |  ✅ r/o   |   ❌  |    ❌   |
|
| Unauthorized access → redirect to dashboard with error toast (no raw 403).
| Read-only enforcement for Guest on Financial/Documents is done in controllers.
|--------------------------------------------------------------------------
*/

// Shorthand role lists used across multiple route groups
const ROLES_ALL_EXCEPT_GUEST = 'System Administrator,Club Adviser,Treasurer,Auditor';
const ROLES_ADMIN_ONLY        = 'System Administrator';

// ── Root redirect ─────────────────────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ── Unauthenticated ───────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => redirect()->route('landing'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Email Verification ────────────────────────────────────────────────────────
Route::get('/email/verify',               [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}',   [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');

// ── Logout (auth only, no verified check) ─────────────────────────────────────
Route::middleware('auth.custom')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ══════════════════════════════════════════════════════════════════════════════
// AUTHENTICATED + VERIFIED
// ══════════════════════════════════════════════════════════════════════════════
Route::middleware(['auth.custom', 'verified'])->group(function () {

    // ── Public API ────────────────────────────────────────────────────────────
    // Available to all authenticated users (used by dropdowns across modules)
    Route::get('/api/financial-categories', [FinancialCategoryController::class, 'apiList'])
        ->name('api.financial-categories.list');

    // ──────────────────────────────────────────────────────────────────────────
    // DASHBOARD — All roles including Guest
    // ──────────────────────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ──────────────────────────────────────────────────────────────────────────
    // MEMBERS — All roles EXCEPT Guest
    // Attempting access as Guest → redirected to dashboard with error toast
    // ──────────────────────────────────────────────────────────────────────────
    Route::middleware('role:' . ROLES_ALL_EXCEPT_GUEST)
        ->prefix('members')
        ->name('members.')
        ->group(function () {
            Route::get('/',                            [MemberController::class, 'index'])->name('index');
            Route::get('/create',                      [MemberController::class, 'create'])->name('create');
            Route::post('/',                           [MemberController::class, 'store'])->name('store');
            Route::get('/check-email',                 [MemberController::class, 'checkEmail'])->name('check-email');
            Route::get('/{id}',                        [MemberController::class, 'show'])->name('show');
            Route::get('/{id}/edit',                   [MemberController::class, 'edit'])->name('edit');
            Route::put('/{id}',                        [MemberController::class, 'update'])->name('update');
            Route::delete('/{id}',                     [MemberController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/edit-history',           [MemberController::class, 'editHistory'])->name('edit-history');
            Route::get('/{id}/position-history-data',  [MemberController::class, 'getPositionHistoryData'])->name('position-history-data');
            Route::post('/{id}/deactivate',            [MemberController::class, 'deactivate'])->name('deactivate');
            Route::post('/{id}/activate',              [MemberController::class, 'activate'])->name('activate');
        });

    // ──────────────────────────────────────────────────────────────────────────
    // APPROVED FINANCIAL REPORTS (Documents) — All roles including Guest (r/o)
    // No role middleware here. Guest read-only enforcement is in DocumentController
    // (e.g. gate checks before store/update/delete actions).
    // ──────────────────────────────────────────────────────────────────────────
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/',                                        [DocumentController::class, 'index'])->name('index');
        Route::get('/create',                                  [DocumentController::class, 'create'])->name('create');
        Route::post('/',                                       [DocumentController::class, 'store'])->name('store');
        Route::get('/{document}',                              [DocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit',                         [DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}',                              [DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}',                           [DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download',                     [DocumentController::class, 'download'])->name('download');
        Route::get('/{document}/preview',                      [DocumentController::class, 'preview'])->name('preview');
        Route::get('/{document}/versions/{version}/download',  [DocumentVersionController::class, 'download'])->name('version.download');
    });

    // Document trash (write actions guarded inside controller)
    Route::get('documents-trash',               [DocumentController::class, 'trash'])->name('documents.trash');
    Route::patch('documents-trash/{id}/restore',[DocumentController::class, 'restore'])->name('documents.restore');
    Route::delete('documents-trash/{id}/force', [DocumentController::class, 'forceDelete'])->name('documents.force-delete');

    // ──────────────────────────────────────────────────────────────────────────
    // FINANCIAL RECORDS — All roles including Guest (r/o)
    // No role middleware here. Guest read-only enforcement is in FinancialController
    // (gate checks before income.create, expense.create, update, delete, etc.)
    // ──────────────────────────────────────────────────────────────────────────
    Route::prefix('financial')->name('financial.')->group(function () {

        // Reports
        Route::post('/report/preview',  [FinancialController::class, 'preview'])->name('report.preview');
        Route::get('/report',           [FinancialController::class, 'reportForm'])->name('report.form');
        Route::post('/report/generate', [FinancialController::class, 'generateReport'])->name('report.generate');

        // Income — write routes guarded in controller for Guest
        Route::get('/income/create',    [IncomeController::class, 'create'])->name('income.create');
        Route::post('/income',          [IncomeController::class, 'store'])->name('income.store');

        // Expense — write routes guarded in controller for Guest
        Route::get('/expense/create',   [ExpenseController::class, 'create'])->name('expense.create');
        Route::post('/expense',         [ExpenseController::class, 'store'])->name('expense.store');

        // Receivables
        Route::get('/receivables',                         [ReceivableController::class, 'index'])->name('receivables');
        Route::get('/receivable/{receivable}',             [ReceivableController::class, 'show'])->name('receivable.show');
        Route::post('/receivable/{receivable}/pay',        [ReceivableController::class, 'recordPayment'])->name('receivable.pay');
        Route::patch('/receivable/{receivable}/mark-paid', [ReceivableController::class, 'markPaid'])->name('receivable.mark-paid');

        // Trash
        Route::get('/trash',                [FinancialController::class, 'trash'])->name('trash');
        Route::patch('/{id}/restore',       [FinancialController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [FinancialController::class, 'forceDelete'])->name('force-delete');

        // Core CRUD — wildcard routes must stay last
        Route::get('/',               [FinancialController::class, 'index'])->name('index');
        Route::get('/{id}',           [FinancialController::class, 'show'])->name('show');
        Route::get('/{id}/edit',      [FinancialController::class, 'edit'])->name('edit');
        Route::put('/{id}',           [FinancialController::class, 'update'])->name('update');
        Route::delete('/{id}',        [FinancialController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/audit',   [FinancialController::class, 'audit'])->name('audit');
        Route::patch('/{id}/approve', [FinancialController::class, 'approve'])->name('approve');
        Route::patch('/{id}/reject',  [FinancialController::class, 'reject'])->name('reject');
    });

    // ──────────────────────────────────────────────────────────────────────────
    // ADMINISTRATION — System Administrator only
    // All sub-modules share the same top-level middleware.
    // ──────────────────────────────────────────────────────────────────────────
    Route::middleware('role:' . ROLES_ADMIN_ONLY)
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Users
            Route::prefix('users')->name('users.')->group(function () {
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

            // Roles
            Route::prefix('roles')->name('roles.')->group(function () {
                Route::get('/',          [AdminController::class, 'roles'])->name('index');
                Route::get('/create',    [AdminController::class, 'createRole'])->name('create');
                Route::post('/',         [AdminController::class, 'storeRole'])->name('store');
                Route::get('/{id}/edit', [AdminController::class, 'editRole'])->name('edit');
                Route::put('/{id}',      [AdminController::class, 'updateRole'])->name('update');
                Route::delete('/{id}',   [AdminController::class, 'destroyRole'])->name('destroy');
                Route::patch('/{role}/toggle-visibility', [AdminController::class, 'toggleRoleVisibility'])
                    ->name('toggle-visibility');
            });

            // Permissions
            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::get('/',       [PermissionController::class, 'index'])->name('index');
                Route::put('/{role}', [PermissionController::class, 'update'])->name('update');
            });

            // Document Categories
            Route::prefix('document-categories')->name('document-categories.')->group(function () {
                Route::get('/',                        [DocumentCategoryController::class, 'index'])->name('index');
                Route::get('/create',                  [DocumentCategoryController::class, 'create'])->name('create');
                Route::post('/',                       [DocumentCategoryController::class, 'store'])->name('store');
                Route::get('/{documentCategory}/edit', [DocumentCategoryController::class, 'edit'])->name('edit');
                Route::put('/{documentCategory}',      [DocumentCategoryController::class, 'update'])->name('update');
                Route::delete('/{documentCategory}',   [DocumentCategoryController::class, 'destroy'])->name('destroy');
            });

            // Document Backups
            Route::prefix('document-backups')->name('document-backups.')->group(function () {
                Route::get('/',                      [DocumentBackupController::class, 'index'])->name('index');
                Route::post('/create',               [DocumentBackupController::class, 'create'])->name('create');
                Route::get('/download/{filename}',   [DocumentBackupController::class, 'download'])->name('download')->where('filename', '.*');
                Route::post('/restore',              [DocumentBackupController::class, 'restore'])->name('restore');
                Route::delete('/destroy/{filename}', [DocumentBackupController::class, 'destroy'])->name('destroy')->where('filename', '.*');
            });

            // Financial Categories
            Route::prefix('financial-categories')->name('financial-categories.')->group(function () {
                Route::get('/',                                    [FinancialCategoryController::class, 'index'])->name('index');
                Route::get('/create',                              [FinancialCategoryController::class, 'create'])->name('create');
                Route::post('/',                                   [FinancialCategoryController::class, 'store'])->name('store');
                Route::get('/{financialCategory}/edit',            [FinancialCategoryController::class, 'edit'])->name('edit');
                Route::put('/{financialCategory}',                 [FinancialCategoryController::class, 'update'])->name('update');
                Route::patch('/{financialCategory}/toggle-active', [FinancialCategoryController::class, 'toggleActive'])->name('toggleActive');
                Route::delete('/{financialCategory}',              [FinancialCategoryController::class, 'destroy'])->name('destroy');
                Route::patch('/restore/{id}',                      [FinancialCategoryController::class, 'restore'])->name('restore');
                Route::delete('/force-delete/{id}',                [FinancialCategoryController::class, 'forceDelete'])->name('forceDelete');
            });

            // Audit Logs
            Route::prefix('auditlogs')->name('auditlogs.')->group(function () {
                Route::get('/', [AuditLogController::class, 'index'])->name('index');
            });

        }); // end admin group

    // ──────────────────────────────────────────────────────────────────────────
    // PROFILE — All roles EXCEPT Guest
    // ──────────────────────────────────────────────────────────────────────────
    Route::middleware('role:' . ROLES_ALL_EXCEPT_GUEST)
        ->prefix('profile')
        ->name('profile.')
        ->group(function () {
            Route::get('/',         [ProfileController::class, 'index'])->name('index');
            Route::put('/',         [ProfileController::class, 'updateProfile'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
            Route::post('/theme',   [ProfileController::class, 'updateTheme'])->name('theme');
        });

}); // end auth + verified

// ── Utility ───────────────────────────────────────────────────────────────────
Route::post('/clear-flash-messages', function () {
    session()->forget(['success', 'error', 'warning', 'info', '_flash']);
    return response()->json(['success' => true]);
})->name('clear-flash');

// ── Password Reset ────────────────────────────────────────────────────────────
Route::get('/password/reset', fn () => view('auth.passwords.email'))->name('password.request');

Route::post('/password/email', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));
    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('/password/reset/{token}', function ($token, Request $request) {
    return view('auth.passwords.reset', ['token' => $token, 'email' => $request->query('email', '')]);
})->name('password.reset');

Route::post('/password/reset', function (Request $request) {
    $request->validate([
        'token'    => 'required',
        'email'    => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        fn ($user, $password) => $user->forceFill(['password' => Hash::make($password)])->save()
    );
    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->name('password.update');

// ── Static pages ──────────────────────────────────────────────────────────────
Route::view('/data-privacy-act', 'pages.data-privacy-act')->name('data-privacy-act');
Route::view('/help',             'pages.help')->name('help');
Route::view('/terms-of-service', 'pages.terms')->name('terms-of-service');

// ── Guest Login ───────────────────────────────────────────────────────────────
Route::post('/guest-login', [AuthController::class, 'guestLogin'])->name('guest.login');

// ── Secure Avatar ─────────────────────────────────────────────────────────────
Route::get('/secure-avatar/{filename}', function ($filename) {
    $path = 'avatars/' . $filename;
    if (! Storage::disk('public')->exists($path)) abort(404);
    return response(Storage::disk('public')->get($path))
        ->header('Content-Type', Storage::disk('public')->mimeType($path));
})->where('filename', '.*');