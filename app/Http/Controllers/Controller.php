<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function requirePermission(string $permission): void
    {
        if (!Auth::user()->hasPermission($permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }
}