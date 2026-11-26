<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Secretary / General Dashboard overview.
     */
    public function index(Request $request): View
    {
        $query = Letter::with(['recipients', 'submitter', 'approver']);

        // Search filter (number, title, event, or student)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('letter_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%")
                  ->orWhereHas('recipients', function ($rq) use ($search) {
                      $rq->where('student_name', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $letters = $query->latest()->paginate(10)->withQueryString();

        // Statistics
        $stats = [
            'total' => Letter::count(),
            'pending' => Letter::where('status', 'pending')->count(),
            'approved' => Letter::where('status', 'approved')->count(),
            'rejected_or_revoked' => Letter::whereIn('status', ['rejected', 'revoked'])->count(),
        ];

        return view('dashboard', compact('letters', 'stats'));
    }
}
