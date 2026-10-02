@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-bold mb-2">Initiate Incentive / Refund Claim</h1>
    <p class="text-slate-600 mb-6">Case: {{ $case->title }}</p>
    <form action="{{ route('claims.store', $case->_id) }}" method="POST" class="space-y-4">@csrf
        <div><label class="block text-sm mb-1">Claim type</label><select name="type" class="w-full rounded border-slate-300"><option value="subsidy">Land cost subsidy</option><option value="refund">Caution deposit refund</option></select></div>
        <div><label class="block text-sm mb-1">Amount</label><input type="number" step="0.01" name="amount" class="w-full rounded border-slate-300" required></div>
        <div><label class="block text-sm mb-1">Remarks</label><textarea name="remarks" class="w-full rounded border-slate-300"></textarea></div>
        <button class="bg-slate-900 text-white px-5 py-3 rounded-lg">Initiate claim</button>
    </form>
</div>
@endsection
