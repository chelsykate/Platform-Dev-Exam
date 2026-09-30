<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $auditLogs = AuditLog::with('user')->latest()->take(25)->get();

        return view('audit-logs.index', compact('auditLogs'));
    }
}
