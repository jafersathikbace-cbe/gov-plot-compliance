@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-6" x-data="{ milestones: [{title:'Investment Completion Milestone',type:'investment',description:'Achieve committed capital investment threshold',due:6,value:1000000,unit:'INR'},{title:'Employment Generation Target',type:'employment',description:'Meet employment target',due:9,value:25,unit:'employees'},{title:'Construction Stage Completion',type:'construction',description:'Complete construction stage',due:12,value:100,unit:'percent'},{title:'Project Start Requirement',type:'time_bound',description:'Project must start within lease condition window',due:12,value:1,unit:'started'}] }">
    <h1 class="text-2xl font-bold mb-6">Create Policy Template</h1>
    <form method="POST" action="{{ route('policies.store') }}" class="space-y-6">@csrf
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-sm mb-1">Name</label><input name="name" class="w-full rounded border-slate-300" required></div>
            <div><label class="block text-sm mb-1">Code</label><input name="code" class="w-full rounded border-slate-300" required></div>
            <div class="md:col-span-2"><label class="block text-sm mb-1">Description</label><textarea name="description" class="w-full rounded border-slate-300"></textarea></div>
            <div><label class="block text-sm mb-1">Warning days</label><input type="number" name="warning_days" value="7" class="w-full rounded border-slate-300" required></div>
        </div>
        <div>
            <div class="flex justify-between items-center mb-3"><h2 class="font-semibold">Milestones</h2><button type="button" class="px-3 py-1 border rounded" @click="milestones.push({title:'',type:'generic',description:'',due:1,value:0,unit:'count'})">Add milestone</button></div>
            <template x-for="(m, i) in milestones" :key="i">
                <div class="border rounded-lg p-4 mb-4 grid md:grid-cols-2 gap-3">
                    <div><label class="block text-sm mb-1">Title</label><input class="w-full rounded border-slate-300" :name="`milestone_titles[${i}]`" x-model="m.title" required></div>
                    <div><label class="block text-sm mb-1">Type</label><select class="w-full rounded border-slate-300" :name="`milestone_types[${i}]`" x-model="m.type"><option value="investment">investment</option><option value="employment">employment</option><option value="construction">construction</option><option value="time_bound">time_bound</option><option value="generic">generic</option></select></div>
                    <div class="md:col-span-2"><label class="block text-sm mb-1">Description</label><input class="w-full rounded border-slate-300" :name="`milestone_descriptions[${i}]`" x-model="m.description"></div>
                    <div><label class="block text-sm mb-1">Due in months</label><input type="number" class="w-full rounded border-slate-300" :name="`milestone_due_months[${i}]`" x-model="m.due" required></div>
                    <div><label class="block text-sm mb-1">Required value</label><input type="number" step="0.01" class="w-full rounded border-slate-300" :name="`milestone_required_values[${i}]`" x-model="m.value" required></div>
                    <div><label class="block text-sm mb-1">Unit</label><input class="w-full rounded border-slate-300" :name="`milestone_units[${i}]`" x-model="m.unit" required></div>
                </div>
            </template>
        </div>
        <button class="bg-slate-900 text-white px-5 py-3 rounded-lg">Save template</button>
    </form>
</div>
@endsection
