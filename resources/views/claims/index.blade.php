@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <h1 class="text-2xl font-bold">Claims Queue</h1>
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left border-b"><th class="p-3">Case</th><th class="p-3">Type</th><th class="p-3">Amount</th><th class="p-3">Status</th><th class="p-3">Action</th></tr></thead>
            <tbody>
                @forelse($claims as $claim)
                    <tr class="border-b"><td class="p-3">{{ $claim->allotment_case_id }}</td><td class="p-3">{{ $claim->type }}</td><td class="p-3">{{ $claim->amount }}</td><td class="p-3">{{ $claim->status }}</td><td class="p-3"><a class="text-blue-700" href="{{ route('claims.review', $claim->_id) }}">Review</a></td></tr>
                @empty
                    <tr><td colspan="5" class="p-3 text-slate-500">No claims found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
