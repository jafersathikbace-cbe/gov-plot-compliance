@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Review Submission</h1>
        <p class="text-slate-600">Case: {{ $case?->title }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div><span class="font-semibold">Investment spent:</span> {{ $submission->investment_spent }}</div>
        <div><span class="font-semibold">Employees hired:</span> {{ $submission->employees_hired }}</div>
        <div><span class="font-semibold">Construction status:</span> {{ $submission->construction_status }}</div>
        <div><span class="font-semibold">Current status:</span> {{ $submission->status }}</div>
        <div class="md:col-span-2"><span class="font-semibold">Remarks:</span> {{ $submission->remarks }}</div>
    </div>
    <div class="flex gap-4 flex-wrap">
        <form method="POST" action="{{ route('submissions.accept', $submission->_id) }}">@csrf<button class="bg-emerald-600 text-white px-4 py-2 rounded">Accept submission</button></form>
        <form method="POST" action="{{ route('submissions.return', $submission->_id) }}" class="flex gap-2 items-center">@csrf<input name="review_remarks" class="rounded border-slate-300" placeholder="Return remarks" required><button class="bg-amber-600 text-white px-4 py-2 rounded">Return with remarks</button></form>
    </div>
</div>
@endsection
