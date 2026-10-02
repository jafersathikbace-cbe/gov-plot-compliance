<?php

namespace App\Http\Controllers;

use App\Models\PolicyTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PolicyTemplateController extends Controller
{
    public function index()
    {
        $templates = PolicyTemplate::orderBy('name')->get();
        return view('policies.index', compact('templates'));
    }

    public function create()
    {
        return view('policies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'warning_days' => ['required', 'integer', 'min:1'],
            'milestone_titles' => ['required', 'array', 'min:1'],
            'milestone_titles.*' => ['required', 'string'],
            'milestone_types' => ['required', 'array'],
            'milestone_due_months' => ['required', 'array'],
            'milestone_required_values' => ['required', 'array'],
            'milestone_units' => ['required', 'array'],
        ]);

        $milestones = [];
        foreach ($data['milestone_titles'] as $i => $title) {
            $milestones[] = [
                'title' => $title,
                'type' => $data['milestone_types'][$i] ?? 'generic',
                'description' => $request->input("milestone_descriptions.$i"),
                'due_in_months' => (int) ($data['milestone_due_months'][$i] ?? 0),
                'required_value' => (float) ($data['milestone_required_values'][$i] ?? 0),
                'unit' => $data['milestone_units'][$i] ?? 'count',
                'evidence_required' => true,
            ];
        }

        PolicyTemplate::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'milestones' => $milestones,
            'rules' => [
                'warning_days' => (int) $data['warning_days'],
                'escalation_after_days' => 1,
            ],
        ]);

        return redirect()->route('policies.index')->with('success', 'Policy template created.');
    }
}
