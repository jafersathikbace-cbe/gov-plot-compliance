@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <h1 class="text-2xl font-bold">Submission Review Queue</h1>
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left border-b"><th class="p-3">Case</th><th class="p-3">Status</th><th class="p-3">Submitted at</th><th class="p-3">Action</th></tr></thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr class="border-b"><td class="p-3">{{ $submission->allotment_case_id }}</td><td class="p-3">{{ $submission->status }}</td><td class="p-3">{{ $submission->submitted_at }}</td><td class="p-3"><a class="text-blue-700" href="{{ route('submissions.review', $submission->_id) }}">Review</a></td></tr>
                @empty
                    <tr><td colspan="4" class="p-3 text-slate-500">No submissions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
