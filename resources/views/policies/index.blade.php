@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Policy Templates</h1>
        <a href="{{ route('policies.create') }}" class="bg-slate-900 text-white px-4 py-2 rounded-lg">Create Template</a>
    </div>
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left border-b"><th class="p-3">Name</th><th class="p-3">Code</th><th class="p-3">Milestones</th></tr></thead>
            <tbody>
                @forelse($templates as $template)
                    <tr class="border-b"><td class="p-3">{{ $template->name }}</td><td class="p-3">{{ $template->code }}</td><td class="p-3">{{ count($template->milestones ?? []) }}</td></tr>
                @empty
                    <tr><td class="p-3 text-slate-500" colspan="3">No templates found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
