<?php

namespace App\Http\Controllers;

use App\Models\AllotmentCase;
use App\Models\CaseMilestone;
use App\Models\IncentiveClaim;
use App\Models\Submission;
use App\Models\Plot;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cases = AllotmentCase::orderBy('created_at', 'desc')->get();

        if ($user->hasRole('allottee')) {
            $cases = AllotmentCase::where('allottee_user_id', (string) $user->id)->orderBy('created_at', 'desc')->get();
        }

        $stats = [
            'plots' => Plot::count(),
            'cases' => $cases->count(),
            'compliant' => $cases->where('status', 'fully_compliant')->count(),
            'at_risk' => $cases->where('status', 'at_risk')->count(),
            'non_compliant' => $cases->whereIn('status', ['non_compliant', 'terminated'])->count(),
            'claims' => IncentiveClaim::count(),
            'submissions' => Submission::count(),
            'upcoming_deadlines' => CaseMilestone::whereIn('status', ['due_soon', 'overdue'])->count(),
        ];

        $milestones = CaseMilestone::orderBy('due_date')->limit(8)->get();
        $pendingSubmissions = Submission::where('status', 'submitted')->orderBy('submitted_at', 'desc')->limit(8)->get();
        $pendingClaims = IncentiveClaim::whereIn('status', ['submitted', 'clarification_required'])->orderBy('created_at', 'desc')->limit(8)->get();

        return view('dashboard', compact('user', 'cases', 'stats', 'milestones', 'pendingSubmissions', 'pendingClaims'));
    }
}
