<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentVersionController;
use App\Http\Controllers\Admin\DocumentCategoryController;
use App\Http\Controllers\Admin\DocumentBackupController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\FinancialController; 
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



// ── Root redirect ─────────────────────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ── Guest ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    // Redirect the dedicated login page to the landing page (which contains the login modal)
    Route::get('/login', function () {
        return redirect()->route('landing');
    })->name('login');
    // Keep the POST route for login form submissions from the landing modal
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
Route::middleware(['auth.custom', 'verified'])->group(function () {

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
            Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
            Route::get('documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');

                    
        // // Temporary closure for version download (bypasses controller autoloading)
        // Route::get('documents/{document}/versions/{version}/download', function ($documentId, $versionId) {
        //     $document = \App\Models\Document::findOrFail($documentId);
        //     $version = \App\Models\DocumentVersion::findOrFail($versionId);
            
        //     if ($version->document_id !== $document->id) {
        //         abort(404);
        //     }
            
        //     // Authorize using policy
        //     if (!auth()->user() && !$document->is_public) {
        //         abort(403);
        //     }
        //     if (auth()->user() && !auth()->user()->can('view', $document)) {
        //         abort(403);
        //     }
            
        //     return \Illuminate\Support\Facades\Storage::disk('private')->download($version->file_path, $version->file_name);
        // })->name('documents.version.download');
        Route::get('documents/{document}/versions/{version}/download', 'App\Http\Controllers\DocumentVersionController@download')
            ->name('documents.version.download');

     
            
            // Trash routes
            Route::get('documents-trash', [DocumentController::class, 'trash'])->name('documents.trash');
            Route::patch('documents-trash/{id}/restore', [DocumentController::class, 'restore'])->name('documents.restore');
            Route::delete('documents-trash/{id}/force', [DocumentController::class, 'forceDelete'])->name('documents.force-delete');
        });

    // ── Financial Records ────────────────────────────────────────────────────
    // Place this BEFORE the admin group, inside the main verified group
    Route::prefix('financial')->name('financial.')->group(function () {
    
        // Index & Show
        Route::get('/',           [FinancialController::class, 'index'])->name('index');
        Route::get('/{id}',       [FinancialController::class, 'show'])->name('show');
    
        // Income
        Route::get('/income/create',  [FinancialController::class, 'createIncome'])->name('income.create');
        Route::post('/income',        [FinancialController::class, 'storeIncome'])->name('income.store');
    
        // Expense
        Route::get('/expense/create', [FinancialController::class, 'createExpense'])->name('expense.create');
        Route::post('/expense',       [FinancialController::class, 'storeExpense'])->name('expense.store');

        //audit
        Route::patch('/{id}/audit', [FinancialController::class, 'audit'])->name('audit');
    
        // Edit / Update
        Route::get('/{id}/edit',      [FinancialController::class, 'edit'])->name('edit');
        Route::put('/{id}',           [FinancialController::class, 'update'])->name('update');
    
        // Approve / Reject
        Route::patch('/{id}/approve', [FinancialController::class, 'approve'])->name('approve');
        Route::patch('/{id}/reject',  [FinancialController::class, 'reject'])->name('reject');
    
        // Delete
        Route::delete('/{id}',        [FinancialController::class, 'destroy'])->name('destroy');
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
                
                Route::patch('/{role}/toggle-visibility', [AdminController::class, 'toggleRoleVisibility'])
                    ->name('toggle-visibility');
            });

        // ── Permissions ───────────────────────────────────────────────────────
        Route::middleware('role:System Administrator,Supreme Admin,Club Adviser')
            ->prefix('permissions')->name('permissions.')
            ->group(function () {
                Route::get('/',       [PermissionController::class, 'index'])->name('index');
                Route::put('/{role}', [PermissionController::class, 'update'])->name('update');
            });

        // ── Document Category ─────────────────────────────────────────────────
        Route::middleware('role:System Administrator')
            ->prefix('document-categories')->name('document-categories.')
            ->group(function () {
                Route::get('/',                    [DocumentCategoryController::class, 'index'])->name('index');
                Route::get('/create',              [DocumentCategoryController::class, 'create'])->name('create');
                Route::post('/',                   [DocumentCategoryController::class, 'store'])->name('store');
                Route::get('/{documentCategory}/edit', [DocumentCategoryController::class, 'edit'])->name('edit');
                Route::put('/{documentCategory}',      [DocumentCategoryController::class, 'update'])->name('update');
                Route::delete('/{documentCategory}',   [DocumentCategoryController::class, 'destroy'])->name('destroy');
            });
        
        // ── Document Backups ──────────────────────────────────────────
        Route::middleware('role:System Administrator')
            ->prefix('document-backups')
            ->name('document-backups.')
            ->group(function () {
                Route::get('/',                        [DocumentBackupController::class, 'index'])->name('index');
                Route::post('/create',                 [DocumentBackupController::class, 'create'])->name('create');
                Route::get('/download/{filename}',     [DocumentBackupController::class, 'download'])->name('download')->where('filename', '.*');
                Route::post('/restore',                [DocumentBackupController::class, 'restore'])->name('restore');
                Route::delete('/destroy/{filename}',   [DocumentBackupController::class, 'destroy'])->name('destroy')->where('filename', '.*');
            });

        // ── Audit Logs ───────────────────────────────────────────────────────
        Route::middleware('role:System Administrator')
            ->prefix('auditlogs')
            ->name('auditlogs.')
            ->group(function () {
                Route::get('/', [AuditLogController::class, 'index'])->name('index');
            });

    }); // ✅ closes the admin group

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

// ── Landing Page Resources ───────────────────────────────────────────────────
Route::view('/data-privacy-act', 'pages.data-privacy-act')->name('data-privacy-act');
Route::view('/help', 'pages.help')->name('help');
Route::view('/terms-of-service', 'pages.terms')->name('terms-of-service');

// ── Login for Guest ───────────────────────────────────────────────────
Route::post('/guest-login', [AuthController::class, 'guestLogin'])->name('guest.login');

// ── Avatar route handling ───────────────────────────────────────────────────
Route::get('/secure-avatar/{filename}', function ($filename) {
    $path = 'avatars/' . $filename;
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return response(Storage::disk('public')->get($path))
        ->header('Content-Type', Storage::disk('public')->mimeType($path));
})->where('filename', '.*');

