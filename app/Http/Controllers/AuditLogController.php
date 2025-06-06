<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query();

        if ($request->has('event')) {
            $query->where('event', $request->event);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(20);

        return response()->json($logs);
    }

    public function show(AuditLog $auditLog)
    {
        return response()->json($auditLog);
    }
}
