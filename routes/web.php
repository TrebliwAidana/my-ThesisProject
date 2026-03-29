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

/*
|--------------------------------------------------------------------------
| Web Routes — VSULHS_SSLG
|--------------------------------------------------------------------------
*/

// ============= TEST ROUTES =============
Route::get('/test-login', function() {
    $email = 'trebliwaidana@gmail.com';
    $password = 'password';
    
    $user = App\Models\User::where('email', $email)->first();
    
    if (!$user) {
        return "User not found: " . $email;
    }
    
    $passwordCheck = Hash::check($password, $user->password);
    
    return [
        'user_exists' => true,
        'user_id' => $user->id,
        'user_email' => $user->email,
        'is_active' => $user->is_active,
        'email_verified' => $user->email_verified_at ? true : false,
        'password_matches' => $passwordCheck,
    ];
});

Route::get('/simple-login-page', function() {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Simple Login Test</title>
        <meta name="csrf-token" content="' . csrf_token() . '">
        <style>
            body { font-family: Arial; padding: 50px; max-width: 400px; margin: 0 auto; }
            input { width: 100%; padding: 8px; margin: 5px 0 15px; border: 1px solid #ddd; border-radius: 4px; }
            button { background: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
            .error { color: red; margin-top: 10px; }
        </style>
    </head>
    <body>
        <h2>Simple Login Test</h2>
        <form method="POST" action="/simple-login-submit">
            <input type="hidden" name="_token" value="' . csrf_token() . '">
            <div>
                <label>Email:</label>
                <input type="email" name="email" value="trebliwaidana@gmail.com" required>
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password" value="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </body>
    </html>
    ';
});

Route::post('/simple-login-submit', function(\Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    
    \Log::info('Simple login attempt', ['email' => $credentials['email']]);
    
    if (Auth::attempt($credentials)) {
        \Log::info('Simple login SUCCESS for: ' . $credentials['email']);
        $request->session()->regenerate();
        return redirect('/dashboard');
    }
    
    \Log::info('Simple login FAILED for: ' . $credentials['email']);
    return back()->withErrors(['email' => 'Invalid credentials']);
});
// ============= END TEST ROUTES =============

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ============= EMAIL VERIFICATION ROUTES - MUST BE OUTSIDE AUTH MIDDLEWARE =============
// These routes need to be accessible without authentication
Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');

// Authenticated routes (basic auth)
Route::middleware('auth.custom')->group(function () {
    
    // Logout (always accessible)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Protected routes (require email verification)
Route::middleware(['auth.custom', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
        Route::get('/{id}/position-history-data', [MemberController::class, 'getPositionHistoryData'])->name('position-history-data');
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

    // Admin routes (Adviser only)
    Route::middleware('role:Adviser')->prefix('admin')->name('admin.')->group(function () {
        // Users Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::get('/create', [AdminController::class, 'createUser'])->name('create');
            Route::post('/', [AdminController::class, 'storeUser'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'editUser'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'destroyUser'])->name('destroy');
            Route::post('/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('reset-password');
            Route::post('/{id}/send-verification', [AdminController::class, 'sendVerificationEmail'])->name('send-verification');
            Route::post('/{id}/verify-manual', [AdminController::class, 'verifyEmailManually'])->name('verify-manual');
        });
        
        // Roles Management
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [AdminController::class, 'roles'])->name('index');
            Route::post('/', [AdminController::class, 'storeRole'])->name('store');
            Route::put('/{id}', [AdminController::class, 'updateRole'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'destroyRole'])->name('destroy');
        });
        
        // Permissions Management
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

Route::post('/clear-flash-messages', function() {
    session()->forget('success');
    session()->forget('error');
    session()->forget('warning');
    session()->forget('info');
    session()->forget('_flash');
    return response()->json(['success' => true]);
})->name('clear-flash');