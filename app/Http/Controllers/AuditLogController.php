<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        // Replace with your actual audit logs view
        return view('audit-logs.index');
    }
}