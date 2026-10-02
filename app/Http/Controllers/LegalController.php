<?php

namespace App\Http\Controllers;

use App\Models\AllotmentCase;
use App\Models\CaseMilestone;
use App\Services\AuditService;
use App\Services\CaseLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function issueNotice(Request $request, string $caseId): RedirectResponse
    {
        $case = AllotmentCase::findOrFail($caseId);
        $case->legal_status = 'notice_issued';
        $case->save();
        AuditService::log($caseId, 'notice_issued', 'allotment_case', $caseId, ['remarks' => $request->input('remarks')]);
        return back()->with('success', 'Legal notice issued.');
    }

    public function grantExtension(Request $request, string $caseId): RedirectResponse
    {
        $data = $request->validate([
            'milestone_id' => ['required', 'string'],
            'extension_days' => ['required', 'integer', 'min:1'],
        ]);

        $case = AllotmentCase::findOrFail($caseId);
        $milestone = CaseMilestone::findOrFail($data['milestone_id']);
        $milestone->due_date = now()->parse($milestone->due_date)->addDays((int) $data['extension_days']);
        $milestone->status = 'pending';
        $milestone->save();

        $case->legal_status = 'extension_granted';
        $case->save();
        AuditService::log($caseId, 'extension_granted', 'case_milestone', (string) $milestone->_id, $data);
        return back()->with('success', 'Extension granted and timeline updated.');
    }

    public function flagLegalReview(string $caseId): RedirectResponse
    {
        $case = AllotmentCase::findOrFail($caseId);
        $case->legal_status = 'legal_review';
        $case->save();
        AuditService::log($caseId, 'legal_review_flagged', 'allotment_case', $caseId);
        return back()->with('success', 'Case flagged for legal review.');
    }

    public function terminate(string $caseId, CaseLifecycleService $caseLifecycleService): RedirectResponse
    {
        $case = AllotmentCase::findOrFail($caseId);
        $case->legal_status = 'terminated';
        $case->status = 'terminated';
        $case->save();
        $caseLifecycleService->updateCaseStatus($case);
        AuditService::log($caseId, 'case_terminated', 'allotment_case', $caseId);
        return back()->with('success', 'Case terminated and enforcement action logged.');
    }
}
