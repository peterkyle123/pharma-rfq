<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharma RFQ — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-100 px-6 py-0 sticky top-0 z-50 shadow-sm">
        <div class="max-w-6xl mx-auto flex items-center justify-between h-14">

            {{-- Logo --}}
            <a href="{{ route('rfqs.index') }}" class="flex items-center gap-2.5 text-blue-600 font-bold text-base">
                <div class="bg-blue-600 text-white rounded-lg w-7 h-7 flex items-center justify-center text-sm">💊</div>
                <span>RFQTracker</span>
                
            </a>

            {{-- Nav links --}}
            <div class="flex items-center gap-1 text-sm">
                <a href="{{ route('rfqs.index') }}"
                   class="px-4 py-2 rounded-lg transition font-medium
                       {{ request()->is('rfqs*') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    RFQ Tracker
                </a>
                <a href="{{ route('agencies.index') }}"
                   class="px-4 py-2 rounded-lg transition font-medium
                       {{ request()->is('agencies*') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Agencies
                </a>
            </div>

        </div>
    </nav>

    {{-- Main content --}}
    <main class="max-w-6xl mx-auto px-6 py-8">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    @livewireScripts
</body>
</html>