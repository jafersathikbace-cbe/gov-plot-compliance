@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex flex-col md:flex-row md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">{{ $case->title }}</h1>
                <div class="text-slate-600 text-sm mt-1">Status: {{ $case->status }} · Legal: {{ $case->legal_status ?? 'none' }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('reports.certificate', $case->_id) }}" class="bg-white border px-4 py-2 rounded-lg">Compliance Certificate</a>
                <form method="POST" action="{{ route('cases.close', $case->_id) }}">@csrf<button class="bg-slate-900 text-white px-4 py-2 rounded-lg">Run Closure Check</button></form>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mt-6 text-sm">
            <div><span class="font-semibold">Plot:</span> {{ $plot?->plot_number }}</div>
            <div><span class="font-semibold">Policy:</span> {{ $template?->name }}</div>
            <div><span class="font-semibold">Construction deadline:</span> {{ $case->construction_deadline }}</div>
            <div><span class="font-semibold">Investment required:</span> {{ $case->investment_required }}</div>
            <div><span class="font-semibold">Employment required:</span> {{ $case->employment_required }}</div>
            <div><span class="font-semibold">Subsidy amount:</span> {{ $case->subsidy_amount }}</div>
        </div>
    </div>

    @if(auth()->user()->hasAnyRole(['super_admin','state_admin']))
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-lg mb-4">Officer and allottee assignment</h2>
        <form method="POST" action="{{ route('cases.assign', $case->_id) }}" class="grid md:grid-cols-3 gap-4">@csrf
            <div><label class="block text-sm mb-1">Allottee</label><select name="allottee_user_id" class="w-full rounded border-slate-300">@foreach($allottees as $u)<option value="{{ $u->id }}" @selected($case->allottee_user_id == $u->id)>{{ $u->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm mb-1">District officer</label><select name="district_officer_id" class="w-full rounded border-slate-300"><option value="">Select</option>@foreach($districtOfficers as $u)<option value="{{ $u->id }}" @selected($case->district_officer_id == $u->id)>{{ $u->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm mb-1">Inspection officer</label><select name="inspection_officer_id" class="w-full rounded border-slate-300"><option value="">Select</option>@foreach($inspectionOfficers as $u)<option value="{{ $u->id }}" @selected($case->inspection_officer_id == $u->id)>{{ $u->name }}</option>@endforeach</select></div>
            <div class="md:col-span-3"><button class="bg-slate-900 text-white px-4 py-2 rounded-lg">Save assignments</button></div>
        </form>
    </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-center mb-4"><h2 class="font-semibold text-lg">Milestones</h2>@if(auth()->user()->hasRole('allottee'))<a href="{{ route('submissions.create', $case->_id) }}" class="text-blue-700">Submit progress</a>@endif</div>
            <div class="space-y-3">
                @foreach($milestones as $m)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between gap-4"><div><div class="font-medium">{{ $m->title }}</div><div class="text-sm text-slate-500">{{ $m->type }} · due {{ \Carbon\Carbon::parse($m->due_date)->format('d M Y') }}</div></div><div class="text-sm font-semibold">{{ $m->status }}</div></div>
                        <div class="text-sm mt-2">Target: {{ $m->required_value }} {{ $m->unit }} · Current: {{ $m->current_value ?? 0 }} {{ $m->unit }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-center mb-4"><h2 class="font-semibold text-lg">Submissions</h2>@if(auth()->user()->hasAnyRole(['district_officer','inspection_officer','state_admin','super_admin']))<a href="{{ route('submissions.queue') }}" class="text-blue-700">Review queue</a>@endif</div>
                <div class="space-y-3">@forelse($submissions as $s)<div class="border rounded-lg p-3"><div class="font-medium">{{ $s->construction_status }}</div><div class="text-sm text-slate-500">{{ $s->status }} · {{ \Carbon\Carbon::parse($s->submitted_at)->format('d M Y H:i') }}</div>@if(auth()->user()->hasAnyRole(['district_officer','inspection_officer','state_admin','super_admin']))<a href="{{ route('submissions.review', $s->_id) }}" class="text-blue-700 text-sm">Review</a>@endif</div>@empty<div class="text-slate-500">No submissions yet.</div>@endforelse</div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-center mb-4"><h2 class="font-semibold text-lg">Claims</h2>@if(auth()->user()->hasAnyRole(['district_officer','state_admin','super_admin']))<a href="{{ route('claims.create', $case->_id) }}" class="text-blue-700">New claim</a>@endif</div>
                <div class="space-y-3">@forelse($claims as $c)<div class="border rounded-lg p-3"><div class="font-medium">{{ ucfirst($c->type) }} · {{ $c->amount }}</div><div class="text-sm text-slate-500">{{ $c->status }}</div>@if(auth()->user()->hasAnyRole(['district_officer','state_admin','super_admin']))<a href="{{ route('claims.review', $c->_id) }}" class="text-blue-700 text-sm">Review</a>@endif</div>@empty<div class="text-slate-500">No claims yet.</div>@endforelse</div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasAnyRole(['super_admin','state_admin']))
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="font-semibold text-lg">Legal and escalation actions</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <form method="POST" action="{{ route('legal.notice', $case->_id) }}" class="border rounded-lg p-4 space-y-3">@csrf<input name="remarks" class="w-full rounded border-slate-300" placeholder="Notice remarks"><button class="bg-amber-600 text-white px-4 py-2 rounded-lg">Issue notice</button></form>
            <form method="POST" action="{{ route('legal.review', $case->_id) }}" class="border rounded-lg p-4"><button class="bg-slate-900 text-white px-4 py-2 rounded-lg">Flag legal review</button>@csrf</form>
            <form method="POST" action="{{ route('legal.extension', $case->_id) }}" class="border rounded-lg p-4 grid grid-cols-2 gap-3">@csrf<select name="milestone_id" class="rounded border-slate-300 col-span-2">@foreach($milestones as $m)<option value="{{ $m->_id }}">{{ $m->title }}</option>@endforeach</select><input type="number" name="extension_days" class="rounded border-slate-300" placeholder="Extension days"><button class="bg-blue-700 text-white px-4 py-2 rounded-lg">Grant extension</button></form>
            <form method="POST" action="{{ route('legal.terminate', $case->_id) }}" class="border rounded-lg p-4">@csrf<button class="bg-rose-700 text-white px-4 py-2 rounded-lg">Terminate case</button></form>
        </div>
    </div>
    @endif
</div>
@endsection
