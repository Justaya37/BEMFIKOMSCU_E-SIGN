<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display activity log history for Chairman / Vice Chairman.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with(['letter', 'user']);

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('details', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('letter', function ($lq) use ($search) {
                      $lq->where('letter_number', 'like', "%{$search}%")
                         ->orWhere('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $actionCounts = [
            'SUBMITTED' => AuditLog::where('action', 'SUBMITTED')->count(),
            'APPROVED' => AuditLog::where('action', 'APPROVED')->count(),
            'REJECTED' => AuditLog::where('action', 'REJECTED')->count(),
            'REVOKED' => AuditLog::where('action', 'REVOKED')->count(),
            'VERIFIED_QR' => AuditLog::where('action', 'VERIFIED_QR')->count(),
            'VERIFIED_HASH_MATCH' => AuditLog::where('action', 'VERIFIED_HASH_MATCH')->count(),
            'VERIFIED_HASH_MISMATCH' => AuditLog::where('action', 'VERIFIED_HASH_MISMATCH')->count(),
        ];

        return view('audit-logs.index', compact('logs', 'actionCounts'));
    }
}
