<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Gov Plot Compliance') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="min-h-screen">
        <nav class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="font-bold text-lg">Gov Plot Compliance</a>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span>{{ auth()->user()->name ?? 'Guest' }}</span>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="bg-white/10 px-3 py-2 rounded">Logout</button>
                    </form>
                @endauth
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 py-6">
            @if(session('success'))
                <div class="mb-4 rounded bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded bg-rose-100 text-rose-800 px-4 py-3">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>
</html>
