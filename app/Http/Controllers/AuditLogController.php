<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $this->requirePermission('audit.view');

        $query = AuditLog::with('user')->latest();

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('user')) {
            $query->where('user_name', 'like', '%' . $request->user . '%');
        }

        if ($request->filled('type')) {
            $query->where('auditable_type', 'like', '%' . $request->type . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        $events = AuditLog::distinct()->pluck('event')->sort()->values();

        return view('admin.auditlogs.index', compact('logs', 'events'));
    }
}