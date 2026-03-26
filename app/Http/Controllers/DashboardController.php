<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ---- Role ----
        $roleColors = [
            'Admin'   => 'bg-red-100 text-red-800',
            'Officer' => 'bg-blue-100 text-blue-800',
            'Auditor' => 'bg-yellow-100 text-yellow-800',
            'Member'  => 'bg-gray-100 text-gray-700',
        ];

        // Get role name as string
        $roleName = $user->role?->name ?? '—';

        // Safely get role color, fallback to gray
        $roleColor = $roleColors[$roleName] ?? 'bg-gray-100 text-gray-700';

        // ---- Status ----
        $status = $user->member?->status;
        $statusDisplay = $status ? ucfirst($status) : '—';

        $statusColors = [
            'active'    => 'bg-green-100 text-green-800',
            'inactive'  => 'bg-gray-100 text-gray-700',
            'suspended' => 'bg-red-100 text-red-700',
        ];

        $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-700';

        // ---- Dates ----
        $joinedAt = $user->member?->joined_at
            ? Carbon::parse($user->member->joined_at)->format('M d, Y')
            : '—';

        $memberSince = $user->created_at
            ? $user->created_at->format('M d, Y')
            : '—';

        return view('dashboard', compact(
            'user',
            'roleName',
            'roleColor',
            'statusDisplay',
            'statusColor',
            'joinedAt',
            'memberSince'
        ));
    }
}