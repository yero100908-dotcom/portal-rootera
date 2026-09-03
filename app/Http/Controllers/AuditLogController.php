<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('action', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        return view('audit_logs.index', compact('logs'));
    }
}
