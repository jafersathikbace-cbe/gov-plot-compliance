<?php

namespace App\Http\Controllers;

use App\Models\AllotmentCase;
use App\Models\CaseMilestone;
use App\Models\Plot;
use App\Models\PolicyTemplate;
use App\Models\Submission;
use App\Models\IncentiveClaim;
use App\Models\User;
use App\Services\AuditService;
use App\Services\CaseLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CaseSetupController extends Controller
{
    public function index()
    {
        $cases = AllotmentCase::orderBy('created_at', 'desc')->get();
        return view('cases.index', compact('cases'));
    }

    public function create()
    {
        $allottees = User::role('allottee')->get();
        $districtOfficers = User::role('district_officer')->get();
        $inspectionOfficers = User::role('inspection_officer')->get();
        $templates = PolicyTemplate::all();

        return view('cases.create', compact('allottees', 'districtOfficers', 'inspectionOfficers', 'templates'));
    }

    public function store(Request $request, CaseLifecycleService $caseLifecycleService): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'plot_number' => ['required', 'string'],
            'area' => ['required', 'numeric'],
            'lease_duration_years' => ['required', 'numeric'],
            'project_type' => ['required', 'string'],
            'required_investment' => ['required', 'numeric'],
            'employment_commitment' => ['required', 'numeric'],
            'construction_deadline' => ['required', 'date'],
            'subsidy_amount' => ['nullable', 'numeric'],
            'caution_deposit_amount' => ['nullable', 'numeric'],
            'allottee_user_id' => ['required'],
            'district_officer_id' => ['nullable'],
            'inspection_officer_id' => ['nullable'],
            'policy_template_id' => ['required'],
            'allotment_order' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png'],
            'lease_deed' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $plot = Plot::create([
            'plot_number' => $validated['plot_number'],
            'area' => (float) $validated['area'],
            'lease_duration_years' => (int) $validated['lease_duration_years'],
            'project_type' => $validated['project_type'],
            'required_investment' => (float) $validated['required_investment'],
            'employment_commitment' => (int) $validated['employment_commitment'],
            'construction_deadline' => $validated['construction_deadline'],
            'financial_conditions' => [
                'subsidy_amount' => (float) ($validated['subsidy_amount'] ?? 0),
                'caution_deposit_amount' => (float) ($validated['caution_deposit_amount'] ?? 0),
            ],
            'status' => 'registered',
        ]);

        $documents = [];
        if ($request->hasFile('allotment_order')) {
            $documents['allotment_order'] = $request->file('allotment_order')->store('case-documents', 'public');
        }
        if ($request->hasFile('lease_deed')) {
            $documents['lease_deed'] = $request->file('lease_deed')->store('case-documents', 'public');
        }

        $case = AllotmentCase::create([
            'plot_id' => (string) $plot->_id,
            'allottee_user_id' => (string) $validated['allottee_user_id'],
            'policy_template_id' => (string) $validated['policy_template_id'],
            'title' => $validated['title'],
            'status' => 'active',
            'investment_required' => (float) $validated['required_investment'],
            'employment_required' => (int) $validated['employment_commitment'],
            'construction_deadline' => $validated['construction_deadline'],
            'subsidy_amount' => (float) ($validated['subsidy_amount'] ?? 0),
            'caution_deposit_amount' => (float) ($validated['caution_deposit_amount'] ?? 0),
            'documents' => $documents,
            'district_officer_id' => $validated['district_officer_id'] ? (string) $validated['district_officer_id'] : null,
            'inspection_officer_id' => $validated['inspection_officer_id'] ? (string) $validated['inspection_officer_id'] : null,
            'legal_status' => 'none',
            'is_archived' => false,
            'activated_at' => now(),
        ]);

        $template = PolicyTemplate::findOrFail($validated['policy_template_id']);
        $caseLifecycleService->generateMilestones($case, $template);

        AuditService::log((string) $case->_id, 'case_created_and_activated', 'allotment_case', (string) $case->_id, [
            'plot' => $validated['plot_number'],
        ]);

        return redirect()->route('cases.show', $case->_id)->with('success', 'Case created, timeline generated, and case activated.');
    }

    public function show(string $caseId)
    {
        $case = AllotmentCase::findOrFail($caseId);
        $plot = Plot::find($case->plot_id);
        $template = PolicyTemplate::find($case->policy_template_id);
        $milestones = CaseMilestone::where('allotment_case_id', $caseId)->orderBy('due_date')->get();
        $submissions = Submission::where('allotment_case_id', $caseId)->orderBy('submitted_at', 'desc')->get();
        $claims = IncentiveClaim::where('allotment_case_id', $caseId)->orderBy('created_at', 'desc')->get();
        $allottees = User::role('allottee')->get();
        $districtOfficers = User::role('district_officer')->get();
        $inspectionOfficers = User::role('inspection_officer')->get();

        return view('cases.show', compact('case', 'plot', 'template', 'milestones', 'submissions', 'claims', 'allottees', 'districtOfficers', 'inspectionOfficers'));
    }

    public function assign(Request $request, string $caseId): RedirectResponse
    {
        $case = AllotmentCase::findOrFail($caseId);
        $case->allottee_user_id = (string) $request->input('allottee_user_id', $case->allottee_user_id);
        $case->district_officer_id = $request->filled('district_officer_id') ? (string) $request->input('district_officer_id') : $case->district_officer_id;
        $case->inspection_officer_id = $request->filled('inspection_officer_id') ? (string) $request->input('inspection_officer_id') : $case->inspection_officer_id;
        $case->save();

        AuditService::log((string) $case->_id, 'officers_assigned', 'allotment_case', (string) $case->_id, $request->only(['allottee_user_id', 'district_officer_id', 'inspection_officer_id']));
        return back()->with('success', 'Assignments updated.');
    }

    public function close(string $caseId, CaseLifecycleService $caseLifecycleService): RedirectResponse
    {
        $case = AllotmentCase::findOrFail($caseId);
        $caseLifecycleService->updateCaseStatus($case);
        if ($case->status === 'fully_compliant') {
            $case->is_archived = true;
            $case->save();
            AuditService::log((string) $case->_id, 'case_archived', 'allotment_case', (string) $case->_id);
        }
        return back()->with('success', 'Case closure check completed.');
    }
}
