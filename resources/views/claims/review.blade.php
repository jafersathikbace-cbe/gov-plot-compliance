@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Review Claim</h1>
        <p class="text-slate-600">Case: {{ $case?->title }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div><span class="font-semibold">Type:</span> {{ $claim->type }}</div>
        <div><span class="font-semibold">Amount:</span> {{ $claim->amount }}</div>
        <div><span class="font-semibold">Status:</span> {{ $claim->status }}</div>
        <div class="md:col-span-2"><span class="font-semibold">Remarks:</span> {{ $claim->remarks }}</div>
    </div>
    <div class="flex gap-4 flex-wrap">
        <form method="POST" action="{{ route('claims.approve', $claim->_id) }}" class="flex gap-2 items-center">@csrf<input name="payment_reference" class="rounded border-slate-300" placeholder="Payment reference"><button class="bg-emerald-600 text-white px-4 py-2 rounded">Approve and record payment</button></form>
        <form method="POST" action="{{ route('claims.clarify', $claim->_id) }}" class="flex gap-2 items-center">@csrf<input name="remarks" class="rounded border-slate-300" placeholder="Clarification remarks" required><button class="bg-amber-600 text-white px-4 py-2 rounded">Return for clarification</button></form>
    </div>
</div>
@endsection
