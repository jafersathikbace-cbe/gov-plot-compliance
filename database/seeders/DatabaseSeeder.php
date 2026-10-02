<?php

namespace Database\Seeders;

use App\Models\AllotmentCase;
use App\Models\CaseMilestone;
use App\Models\Plot;
use App\Models\PolicyTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@plotcompliance.local',
                'role' => 'super_admin',
            ],
            [
                'name' => 'State Admin',
                'email' => 'stateadmin@plotcompliance.local',
                'role' => 'state_admin',
            ],
            [
                'name' => 'District Officer',
                'email' => 'district@plotcompliance.local',
                'role' => 'district_officer',
            ],
            [
                'name' => 'Inspection Officer',
                'email' => 'inspection@plotcompliance.local',
                'role' => 'inspection_officer',
            ],
            [
                'name' => 'Demo Allottee',
                'email' => 'allottee@plotcompliance.local',
                'role' => 'allottee',
            ],
        ];

        $created = [];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('Password@123'),
                    'email_verified_at' => now(),
                    'role' => $data['role'],
                ]
            );

            $user->forceFill([
                'name' => $data['name'],
                'role' => $data['role'],
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            $created[$data['role']] = $user;
        }

        $template = PolicyTemplate::firstOrCreate(
            ['code' => 'STD-GOV-001'],
            [
                'name' => 'Standard Government Allotment Policy',
                'description' => 'Default template covering investment, employment, construction and time-bound obligations.',
                'milestones' => [
                    [
                        'type' => 'investment',
                        'title' => 'Investment Completion Milestone',
                        'description' => 'Achieve committed capital investment threshold.',
                        'due_in_months' => 6,
                        'required_value' => 1000000,
                        'unit' => 'INR',
                        'evidence_required' => true,
                    ],
                    [
                        'type' => 'employment',
                        'title' => 'Employment Generation Target',
                        'description' => 'Meet employment generation requirement.',
                        'due_in_months' => 9,
                        'required_value' => 25,
                        'unit' => 'employees',
                        'evidence_required' => true,
                    ],
                    [
                        'type' => 'construction',
                        'title' => 'Construction Stage Completion',
                        'description' => 'Complete required construction milestone.',
                        'due_in_months' => 12,
                        'required_value' => 100,
                        'unit' => 'percent',
                        'evidence_required' => true,
                    ],
                    [
                        'type' => 'time_bound',
                        'title' => 'Project Start Requirement',
                        'description' => 'Project must start within the specified lease condition window.',
                        'due_in_months' => 12,
                        'required_value' => 1,
                        'unit' => 'started',
                        'evidence_required' => true,
                    ],
                ],
                'rules' => [
                    'warning_days' => 7,
                    'escalation_after_days' => 1,
                ],
            ]
        );

        if (! AllotmentCase::where('title', 'Demo Industrial Plot Compliance Case')->exists()) {
            $plot = Plot::create([
                'plot_number' => 'PLOT-001',
                'area' => 2500,
                'lease_duration_years' => 30,
                'project_type' => 'Industrial Unit',
                'required_investment' => 1000000,
                'employment_commitment' => 25,
                'construction_deadline' => now()->addMonths(12)->toDateString(),
                'financial_conditions' => [
                    'subsidy_amount' => 150000,
                    'caution_deposit_amount' => 50000,
                ],
                'status' => 'registered',
            ]);

            $case = AllotmentCase::create([
                'plot_id' => (string) $plot->_id,
                'allottee_user_id' => (string) $created['allottee']->id,
                'policy_template_id' => (string) $template->_id,
                'title' => 'Demo Industrial Plot Compliance Case',
                'status' => 'active',
                'investment_required' => 1000000,
                'employment_required' => 25,
                'construction_deadline' => now()->addMonths(12)->toDateString(),
                'subsidy_amount' => 150000,
                'caution_deposit_amount' => 50000,
                'documents' => [],
                'district_officer_id' => (string) $created['district_officer']->id,
                'inspection_officer_id' => (string) $created['inspection_officer']->id,
                'timeline_generated_at' => now(),
                'activated_at' => now(),
                'legal_status' => 'none',
                'is_archived' => false,
            ]);

            foreach ($template->milestones as $rule) {
                CaseMilestone::create([
                    'allotment_case_id' => (string) $case->_id,
                    'type' => $rule['type'],
                    'title' => $rule['title'],
                    'description' => $rule['description'],
                    'due_date' => now()->addMonths((int) $rule['due_in_months']),
                    'required_value' => $rule['required_value'],
                    'current_value' => 0,
                    'unit' => $rule['unit'],
                    'status' => 'pending',
                    'evidence_required' => true,
                ]);
            }
        }
    }
}