<?php

namespace App\Http\Controllers;

use App\Models\AllotmentCase;
use App\Models\CaseMilestone;
use App\Models\Submission;
use App\Services\AuditService;
use App\Services\CaseLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function queue()
    {
        $submissions = Submission::orderBy('submitted_at', 'desc')->get();
        return view('submissions.index', compact('submissions'));
    }

    public function create(string $caseId)
    {
        $case = AllotmentCase::findOrFail($caseId);
        $milestones = CaseMilestone::where('allotment_case_id', $caseId)->orderBy('due_date')->get();
        return view('submissions.create', compact('case', 'milestones'));
    }

    public function store(Request $request, string $caseId): RedirectResponse
    {
        $validated = $request->validate([
            'milestone_id' => ['nullable', 'string'],
            'investment_spent' => ['required', 'numeric'],
            'employees_hired' => ['required', 'numeric'],
            'construction_status' => ['required', 'string'],
            'remarks' => ['nullable', 'string'],
            'evidence_files.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $files = [];
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $files[] = $file->store('submission-evidence', 'public');
            }
        }

        $submission = Submission::create([
            'allotment_case_id' => $caseId,
            'milestone_id' => $validated['milestone_id'] ?? null,
            'submitted_by' => (string) Auth::id(),
            'investment_spent' => (float) $validated['investment_spent'],
            'employees_hired' => (int) $validated['employees_hired'],
            'construction_status' => $validated['construction_status'],
            'remarks' => $validated['remarks'] ?? null,
            'evidence_files' => $files,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        AuditService::log($caseId, 'submission_created', 'submission', (string) $submission->_id, [
            'submitted_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Progress submitted successfully.');
    }

    public function review(string $submissionId)
    {
        $submission = Submission::findOrFail($submissionId);
        $case = AllotmentCase::find($submission->allotment_case_id);
        return view('submissions.review', compact('submission', 'case'));
    }

    public function accept(string $submissionId, CaseLifecycleService $caseLifecycleService): RedirectResponse
    {
        $submission = Submission::findOrFail($submissionId);
        $submission->status = 'accepted';
        $submission->reviewed_by = (string) Auth::id();
        $submission->reviewed_at = now();
        $submission->save();

        $milestones = CaseMilestone::where('allotment_case_id', $submission->allotment_case_id)->get();
        foreach ($milestones as $milestone) {
            if ($milestone->type === 'investment') {
                $milestone->current_value = max((float) $milestone->current_value, (float) $submission->investment_spent);
            }
            if ($milestone->type === 'employment') {
                $milestone->current_value = max((float) $milestone->current_value, (float) $submission->employees_hired);
            }
            if (in_array($milestone->type, ['construction', 'time_bound'], true) && str_contains(strtolower($submission->construction_status), 'complete')) {
                $milestone->current_value = (float) $milestone->required_value;
            }
            if ((float) $milestone->current_value >= (float) $milestone->required_value) {
                $milestone->status = 'verified';
                $milestone->verified_at = now();
                $milestone->verified_by = (string) Auth::id();
            }
            $milestone->save();
        }

        $case = AllotmentCase::findOrFail($submission->allotment_case_id);
        $caseLifecycleService->updateCaseStatus($case);

        AuditService::log($submission->allotment_case_id, 'submission_accepted', 'submission', (string) $submission->_id);

        return redirect()->route('cases.show', $case->_id)->with('success', 'Submission accepted and milestones updated.');
    }

    public function returnWithRemarks(Request $request, string $submissionId): RedirectResponse
    {
        $request->validate(['review_remarks' => ['required', 'string']]);
        $submission = Submission::findOrFail($submissionId);
        $submission->status = 'returned';
        $submission->reviewed_by = (string) Auth::id();
        $submission->reviewed_at = now();
        $submission->review_remarks = $request->input('review_remarks');
        $submission->save();

        AuditService::log($submission->allotment_case_id, 'submission_returned', 'submission', (string) $submission->_id, [
            'remarks' => $submission->review_remarks,
        ]);

        return redirect()->route('cases.show', $submission->allotment_case_id)->with('success', 'Submission returned with remarks.');
    }
}
