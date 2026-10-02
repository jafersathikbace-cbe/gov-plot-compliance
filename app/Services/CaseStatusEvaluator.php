<?php

namespace App\Services;

class CaseStatusEvaluator
{
    /**
     * @param array<int, array{status?: string}> $milestones
     */
    public function evaluate(string $legalStatus, array $milestones): string
    {
        $statuses = array_map(
            static fn (array $milestone): string => $milestone['status'] ?? 'pending',
            $milestones
        );

        if ($legalStatus === 'terminated') {
            return 'terminated';
        }

        if ($statuses !== [] && count(array_filter($statuses, static fn (string $status): bool => $status === 'verified')) === count($statuses)) {
            return 'fully_compliant';
        }

        if (in_array('overdue', $statuses, true)) {
            return 'non_compliant';
        }

        if (in_array('due_soon', $statuses, true)) {
            return 'at_risk';
        }

        return 'active';
    }
}
