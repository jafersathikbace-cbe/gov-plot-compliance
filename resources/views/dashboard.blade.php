@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:justify-between md:items-center">
        <div>
            <h1 class="text-2xl font-bold">Government Plot Compliance Dashboard</h1>
            <p class="text-slate-600">Signed in as {{ $user->name }} · {{ $user->getRoleNames()->implode(', ') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(auth()->user()->hasAnyRole(['super_admin','state_admin']))
                <a href="{{ route('cases.create') }}" class="bg-slate-900 text-white px-4 py-2 rounded-lg">New Case Wizard</a>
                <a href="{{ route('policies.index') }}" class="bg-white border px-4 py-2 rounded-lg">Policy Templates</a>
            @endif
            <a href="{{ route('reports.monthly') }}" class="bg-white border px-4 py-2 rounded-lg">Monthly PDF</a>
            <a href="{{ route('reports.nonCompliance') }}" class="bg-white border px-4 py-2 rounded-lg">Non-compliance PDF</a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($stats as $label => $value)
            <div class="bg-white rounded-xl shadow p-5">
                <div class="text-sm uppercase tracking-wide text-slate-500">{{ str_replace('_', ' ', $label) }}</div>
                <div class="text-3xl font-bold mt-2">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-xl shadow p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-lg">Case Register</h2>
                <a href="{{ route('cases.index') }}" class="text-sm text-blue-700">View all</a>
            </div>
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">Case</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cases as $case)
                            <tr class="border-b align-top">
                                <td class="py-3">
                                    <div class="font-medium">{{ $case->title }}</div>
                                    <div class="text-slate-500 text-xs">Case ID: {{ $case->_id }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded text-xs @if($case->status === 'fully_compliant') bg-emerald-100 text-emerald-700 @elseif($case->status === 'at_risk') bg-amber-100 text-amber-700 @elseif(in_array($case->status, ['non_compliant','terminated'])) bg-rose-100 text-rose-700 @else bg-slate-100 text-slate-700 @endif">{{ str_replace('_', ' ', $case->status) }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="flex flex-wrap gap-3 text-sm">
                                        <a href="{{ route('cases.show', $case->_id) }}" class="text-blue-700">Open</a>
                                        @if(auth()->user()->hasRole('allottee'))
                                            <a href="{{ route('submissions.create', $case->_id) }}" class="text-emerald-700">Submit</a>
                                        @endif
                                        @if(auth()->user()->hasAnyRole(['district_officer','state_admin','super_admin']))
                                            <a href="{{ route('claims.create', $case->_id) }}" class="text-violet-700">Claim</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-slate-500">No cases created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="font-semibold text-lg mb-4">Milestone Monitor</h2>
                <div class="space-y-3">
                    @forelse($milestones as $milestone)
                        <div class="border rounded-lg p-3">
                            <div class="font-medium">{{ $milestone->title }}</div>
                            <div class="text-sm text-slate-500">Due {{ \Carbon\Carbon::parse($milestone->due_date)->format('d M Y') }}</div>
                            <div class="mt-1 text-sm">Status: <span class="font-semibold">{{ $milestone->status }}</span></div>
                        </div>
                    @empty
                        <div class="text-slate-500">No milestones yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="font-semibold text-lg mb-4">Review Queue</h2>
                <div class="space-y-2 text-sm">
                    <div>Pending submissions: <span class="font-semibold">{{ $pendingSubmissions->count() }}</span></div>
                    <div>Pending claims: <span class="font-semibold">{{ $pendingClaims->count() }}</span></div>
                    @if(auth()->user()->hasAnyRole(['district_officer','inspection_officer','state_admin','super_admin']))
                        <a href="{{ route('submissions.queue') }}" class="text-blue-700 inline-block">Open submission queue</a>
                    @endif
                    @if(auth()->user()->hasAnyRole(['district_officer','state_admin','super_admin']))
                        <a href="{{ route('claims.index') }}" class="text-blue-700 inline-block">Open claim queue</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
