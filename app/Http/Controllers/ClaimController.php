<?php

namespace App\Http\Controllers;

use App\Models\AllotmentCase;
use App\Models\IncentiveClaim;
use App\Services\AuditService;
use App\Services\CaseLifecycleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    public function index()
    {
        $claims = IncentiveClaim::orderBy('created_at', 'desc')->get();
        return view('claims.index', compact('claims'));
    }

    public function create(string $caseId)
    {
        $case = AllotmentCase::findOrFail($caseId);
        return view('claims.create', compact('case'));
    }

    public function store(Request $request, string $caseId, CaseLifecycleService $caseLifecycleService): RedirectResponse
    {
        $case = AllotmentCase::findOrFail($caseId);

        if (! $caseLifecycleService->verifyEligibleForClaim($case)) {
            return back()->withErrors(['claim' => 'Case is not yet eligible for refund or subsidy claim.']);
        }

        $validated = $request->validate([
            'type' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'remarks' => ['nullable', 'string'],
        ]);

        $claim = IncentiveClaim::create([
            'allotment_case_id' => $caseId,
            'type' => $validated['type'],
            'amount' => (float) $validated['amount'],
            'status' => 'submitted',
            'submitted_by' => (string) Auth::id(),
            'remarks' => $validated['remarks'] ?? null,
        ]);

        AuditService::log($caseId, 'claim_initiated', 'incentive_claim', (string) $claim->_id, $validated);

        return redirect()->route('claims.review', $claim->_id)->with('success', 'Claim initiated.');
    }

    public function review(string $claimId)
    {
        $claim = IncentiveClaim::findOrFail($claimId);
        $case = AllotmentCase::find($claim->allotment_case_id);
        return view('claims.review', compact('claim', 'case'));
    }

    public function approve(Request $request, string $claimId): RedirectResponse
    {
        $claim = IncentiveClaim::findOrFail($claimId);
        $claim->status = 'paid';
        $claim->reviewed_by = (string) Auth::id();
        $claim->payment_reference = $request->input('payment_reference', 'PAY-'.strtoupper(substr(md5((string) now()), 0, 10)));
        $claim->approved_at = now();
        $claim->paid_at = now();
        $claim->save();

        AuditService::log($claim->allotment_case_id, 'claim_approved_and_paid', 'incentive_claim', (string) $claim->_id, [
            'payment_reference' => $claim->payment_reference,
        ]);

        return redirect()->route('claims.memo', $claim->_id)->with('success', 'Claim approved and payment recorded.');
    }

    public function clarify(Request $request, string $claimId): RedirectResponse
    {
        $request->validate(['remarks' => ['required', 'string']]);
        $claim = IncentiveClaim::findOrFail($claimId);
        $claim->status = 'clarification_required';
        $claim->reviewed_by = (string) Auth::id();
        $claim->remarks = $request->input('remarks');
        $claim->save();

        AuditService::log($claim->allotment_case_id, 'claim_returned_for_clarification', 'incentive_claim', (string) $claim->_id, [
            'remarks' => $claim->remarks,
        ]);

        return redirect()->route('cases.show', $claim->allotment_case_id)->with('success', 'Claim returned for clarification.');
    }

    public function memoPdf(string $claimId)
    {
        $claim = IncentiveClaim::findOrFail($claimId);
        $case = AllotmentCase::find($claim->allotment_case_id);
        $pdf = Pdf::loadView('pdf.approval-memo', compact('claim', 'case'));
        return $pdf->download('approval-memo-'.$claimId.'.pdf');
    }
}
