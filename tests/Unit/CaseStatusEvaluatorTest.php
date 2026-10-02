<?php

namespace Tests\Unit;

use App\Services\CaseStatusEvaluator;
use PHPUnit\Framework\TestCase;

class CaseStatusEvaluatorTest extends TestCase
{
    private CaseStatusEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new CaseStatusEvaluator();
    }

    public function test_terminated_case_wins_over_milestone_state(): void
    {
        $this->assertSame('terminated', $this->evaluator->evaluate('terminated', [
            ['status' => 'verified'],
        ]));
    }

    public function test_all_verified_milestones_make_case_fully_compliant(): void
    {
        $this->assertSame('fully_compliant', $this->evaluator->evaluate('active', [
            ['status' => 'verified'],
            ['status' => 'verified'],
        ]));
    }

    public function test_overdue_milestone_marks_case_non_compliant(): void
    {
        $this->assertSame('non_compliant', $this->evaluator->evaluate('active', [
            ['status' => 'verified'],
            ['status' => 'overdue'],
        ]));
    }

    public function test_due_soon_milestone_marks_case_at_risk(): void
    {
        $this->assertSame('at_risk', $this->evaluator->evaluate('active', [
            ['status' => 'due_soon'],
        ]));
    }

    public function test_empty_or_pending_milestones_keep_case_active(): void
    {
        $this->assertSame('active', $this->evaluator->evaluate('active', []));
        $this->assertSame('active', $this->evaluator->evaluate('active', [
            ['status' => 'pending'],
        ]));
    }
}
