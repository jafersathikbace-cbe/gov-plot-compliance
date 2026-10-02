<?php

namespace App\Services;

use App\Models\AllotmentCase;
use App\Models\CaseMilestone;
use App\Models\PolicyTemplate;
use Carbon\Carbon;

class CaseLifecycleService
{
    public function __construct(private readonly CaseStatusEvaluator $statusEvaluator)
    {
    }
    public function generateMilestones(AllotmentCase $case, PolicyTemplate $template): void
    {
        $rules = $template->milestones ?? [];
        $startDate = Carbon::parse($case->activated_at ?? now());

        foreach ($rules as $index => $rule) {
            CaseMilestone::create([
                'allotment_case_id' => (string) $case->_id,
                'type' => $rule['type'] ?? 'generic',
                'title' => $rule['title'] ?? 'Milestone '.($index + 1),
                'description' => $rule['description'] ?? null,
                'due_date' => $startDate->copy()->addMonths((int) ($rule['due_in_months'] ?? 0)),
                'required_value' => (float) ($rule['required_value'] ?? 0),
                'current_value' => 0,
                'unit' => $rule['unit'] ?? 'count',
                'status' => 'pending',
                'evidence_required' => $rule['evidence_required'] ?? true,
            ]);
        }

        $case->timeline_generated_at = now();
        $case->save();

        AuditService::log((string) $case->_id, 'timeline_generated', 'allotment_case', (string) $case->_id, [
            'template' => $template->name,
        ]);
    }

    public function updateCaseStatus(AllotmentCase $case): void
    {
        $milestones = CaseMilestone::where('allotment_case_id', (string) $case->_id)->get();
        $case->status = $this->statusEvaluator->evaluate(
            (string) $case->legal_status,
            $milestones->map(fn ($milestone) => ['status' => $milestone->status])->all()
        );

        if ($case->status === 'fully_compliant') {
            $case->closed_at = now();
        }

        $case->save();

        AuditService::log((string) $case->_id, 'case_status_updated', 'allotment_case', (string) $case->_id, [
            'status' => $case->status,
        ]);
    }

    public function verifyEligibleForClaim(AllotmentCase $case): bool
    {
        return in_array($case->status, ['fully_compliant', 'active', 'at_risk'], true)
            && CaseMilestone::where('allotment_case_id', (string) $case->_id)->where('status', 'verified')->count() > 0;
    }
}
