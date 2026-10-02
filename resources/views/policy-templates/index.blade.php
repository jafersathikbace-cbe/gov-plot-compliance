@extends('layouts.app')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Policy Templates</h1>
        <a href="{{ route('templates.create') }}" class="bg-slate-900 text-white px-4 py-2 rounded">New Template</a>
    </div>
    <div class="overflow-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-2">Code</th>
                    <th class="py-2">Name</th>
                    <th class="py-2">Milestones</th>
                    <th class="py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr class="border-b">
                        <td class="py-2">{{ $template->code }}</td>
                        <td class="py-2">{{ $template->name }}</td>
                        <td class="py-2">{{ count($template->milestones ?? []) }}</td>
                        <td class="py-2"><a href="{{ route('templates.edit', $template->_id) }}" class="text-blue-600">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-slate-500">No templates found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
