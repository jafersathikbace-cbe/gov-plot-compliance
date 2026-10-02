@extends('layouts.app')

@section('content')
@php
$defaultJson = json_encode($template->milestones ?? [
    ['type' => 'investment', 'title' => 'Investment completion', 'description' => 'Reach committed investment', 'due_in_months' => 6, 'required_value' => 1000000, 'unit' => 'INR', 'evidence_required' => true],
    ['type' => 'employment', 'title' => 'Employment generation', 'description' => 'Meet employment target', 'due_in_months' => 9, 'required_value' => 25, 'unit' => 'employees', 'evidence_required' => true],
    ['type' => 'construction', 'title' => 'Construction milestone', 'description' => 'Complete construction stage', 'due_in_months' => 12, 'required_value' => 100, 'unit' => 'percent', 'evidence_required' => true],
    ['type' => 'time_bound', 'title' => 'Project start requirement', 'description' => 'Project start within 12 months', 'due_in_months' => 12, 'required_value' => 1, 'unit' => 'started', 'evidence_required' => true],
], JSON_PRETTY_PRINT);
@endphp
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-bold mb-4">{{ $template ? 'Edit Policy Template' : 'Create Policy Template' }}</h1>
    <form action="{{ $template ? route('templates.update', $template->_id) : route('templates.store') }}" method="POST" class="space-y-4">
        @csrf
        @if($template)
            @method('PUT')
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Name</label>
                <input name="name" value="{{ old('name', $template->name ?? '') }}" class="w-full rounded border-slate-300" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Code</label>
                <input name="code" value="{{ old('code', $template->code ?? '') }}" class="w-full rounded border-slate-300" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Warning Days</label>
                <input type="number" name="warning_days" value="{{ old('warning_days', $template->rules['warning_days'] ?? 7) }}" class="w-full rounded border-slate-300" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Escalation After Days</label>
                <input type="number" name="escalation_after_days" value="{{ old('escalation_after_days', $template->rules['escalation_after_days'] ?? 1) }}" class="w-full rounded border-slate-300" required>
            </div>
        </div>
        <div>
            <label class="block text-sm mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full rounded border-slate-300">{{ old('description', $template->description ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm mb-1">Milestones JSON</label>
            <textarea name="milestones_json" rows="16" class="w-full rounded border-slate-300 font-mono text-sm" required>{{ old('milestones_json', $defaultJson) }}</textarea>
        </div>
        <div class="flex justify-end">
            <button class="bg-slate-900 text-white px-5 py-3 rounded-lg">Save Template</button>
        </div>
    </form>
</div>
@endsection
