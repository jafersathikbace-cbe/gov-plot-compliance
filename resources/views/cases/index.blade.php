@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Allotment Cases</h1>
        @if(auth()->user()->hasAnyRole(['super_admin','state_admin']))
            <a href="{{ route('cases.create') }}" class="bg-slate-900 text-white px-4 py-2 rounded-lg">Create Case</a>
        @endif
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left border-b"><th class="p-3">Title</th><th class="p-3">Status</th><th class="p-3">Actions</th></tr></thead>
            <tbody>
                @forelse($cases as $case)
                    <tr class="border-b"><td class="p-3">{{ $case->title }}</td><td class="p-3">{{ $case->status }}</td><td class="p-3"><a class="text-blue-700" href="{{ route('cases.show', $case->_id) }}">Open</a></td></tr>
                @empty
                    <tr><td class="p-3 text-slate-500" colspan="3">No cases found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
