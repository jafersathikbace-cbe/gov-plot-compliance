@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-bold mb-2">Case Creation Wizard</h1>
    <p class="text-slate-600 mb-6">This wizard covers plot setup, allottee mapping, document upload, policy selection, officer assignment, and activation.</p>
    <form action="{{ route('cases.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <div class="md:col-span-2"><h2 class="font-semibold">1. Plot and project details</h2></div>
        <div><label class="block text-sm mb-1">Case title</label><input name="title" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Plot number</label><input name="plot_number" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Area</label><input type="number" step="0.01" name="area" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Lease duration (years)</label><input type="number" name="lease_duration_years" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Project type</label><input name="project_type" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Construction deadline</label><input type="date" name="construction_deadline" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Required investment</label><input type="number" step="0.01" name="required_investment" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Employment commitment</label><input type="number" name="employment_commitment" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Subsidy amount</label><input type="number" step="0.01" name="subsidy_amount" class="w-full rounded border-slate-300"></div>
        <div><label class="block text-sm mb-1">Caution deposit amount</label><input type="number" step="0.01" name="caution_deposit_amount" class="w-full rounded border-slate-300"></div>

        <div class="md:col-span-2 mt-4"><h2 class="font-semibold">2. Allottee and policy mapping</h2></div>
        <div><label class="block text-sm mb-1">Allottee</label><select name="allottee_user_id" class="w-full rounded border-slate-300" required>@foreach($allottees as $user)<option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>@endforeach</select></div>
        <div><label class="block text-sm mb-1">Policy template</label><select name="policy_template_id" class="w-full rounded border-slate-300" required>@foreach($templates as $template)<option value="{{ $template->_id }}">{{ $template->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm mb-1">District officer</label><select name="district_officer_id" class="w-full rounded border-slate-300"><option value="">Select</option>@foreach($districtOfficers as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
        <div><label class="block text-sm mb-1">Inspection officer</label><select name="inspection_officer_id" class="w-full rounded border-slate-300"><option value="">Select</option>@foreach($inspectionOfficers as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>

        <div class="md:col-span-2 mt-4"><h2 class="font-semibold">3. Documents</h2></div>
        <div><label class="block text-sm mb-1">Allotment order</label><input type="file" name="allotment_order" class="w-full rounded border-slate-300"></div>
        <div><label class="block text-sm mb-1">Lease deed</label><input type="file" name="lease_deed" class="w-full rounded border-slate-300"></div>

        <div class="md:col-span-2 flex justify-end mt-4"><button class="bg-slate-900 text-white px-5 py-3 rounded-lg">Create case, generate milestones, activate</button></div>
    </form>
</div>
@endsection
