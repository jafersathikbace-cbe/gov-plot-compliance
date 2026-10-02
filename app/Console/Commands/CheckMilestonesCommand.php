<?php

namespace App\Console\Commands;

use App\Models\AllotmentCase;
use App\Models\CaseMilestone;
use App\Models\User;
use App\Services\AuditService;
use App\Services\CaseLifecycleService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckMilestonesCommand extends Command
{
    protected $signature = 'compliance:check-milestones';
    protected $description = 'Check milestone due dates and update warning / overdue statuses';

    public function handle(NotificationService $notificationService, CaseLifecycleService $caseLifecycleService): int
    {
        $this->info('Checking milestones...');

        foreach (CaseMilestone::all() as $milestone) {
            $case = AllotmentCase::find($milestone->allotment_case_id);
            if (! $case || in_array($milestone->status, ['verified', 'terminated'], true)) {
                continue;
            }

            $dueDate = Carbon::parse($milestone->due_date);
            $daysDiff = now()->diffInDays($dueDate, false);

            if ($daysDiff < 0) {
                $milestone->status = 'overdue';
                $milestone->overdue_alert_sent_at = now();
                $recipient = $case->district_officer_id ? User::find($case->district_officer_id) : null;
                $notificationService->sendCaseAlert($case, 'Red Alert: milestone overdue', 'Milestone '.$milestone->title.' is overdue.', $recipient);
                AuditService::log((string) $case->_id, 'milestone_overdue', 'case_milestone', (string) $milestone->_id, ['title' => $milestone->title]);
            } elseif ($daysDiff <= 7 && $milestone->status !== 'verified') {
                $milestone->status = 'due_soon';
                $milestone->warning_sent_at = now();
                $recipient = $case->allottee_user_id ? User::find($case->allottee_user_id) : null;
                $notificationService->sendCaseAlert($case, 'Yellow Alert: milestone due soon', 'Milestone '.$milestone->title.' is due within 7 days.', $recipient);
                AuditService::log((string) $case->_id, 'milestone_due_soon', 'case_milestone', (string) $milestone->_id, ['title' => $milestone->title]);
            } elseif ($milestone->status !== 'verified') {
                $milestone->status = 'pending';
            }

            $milestone->save();
            $caseLifecycleService->updateCaseStatus($case);
        }

        $this->info('Milestone monitoring complete.');
        return self::SUCCESS;
    }
}
