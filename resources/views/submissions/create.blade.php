@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-bold mb-2">Submit Compliance Progress</h1>
    <p class="text-slate-600 mb-6">Case: {{ $case->title }}</p>

    <form action="{{ route('submissions.store', $case->_id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1">Related milestone</label>
            <select name="milestone_id" class="w-full rounded border-slate-300">
                <option value="">General update</option>
                @foreach($milestones as $milestone)
                    <option value="{{ $milestone->_id }}">{{ $milestone->title }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="block text-sm mb-1">Investment spent</label><input type="number" step="0.01" name="investment_spent" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Employees hired</label><input type="number" name="employees_hired" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Construction status</label><input name="construction_status" class="w-full rounded border-slate-300" placeholder="Foundation complete / Construction complete" required></div>
        <div><label class="block text-sm mb-1">Remarks</label><textarea name="remarks" class="w-full rounded border-slate-300"></textarea></div>
        <div><label class="block text-sm mb-1">Evidence files</label><input type="file" name="evidence_files[]" multiple class="w-full rounded border-slate-300"></div>
        <button class="bg-slate-900 text-white px-5 py-3 rounded-lg">Submit progress and evidence</button>
    </form>
</div>
@endsection
