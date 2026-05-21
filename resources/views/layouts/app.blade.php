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
<body class="bg-gray-100 text-gray-900 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-blue-600 font-bold text-lg">💊 RFQTracker</span>
            <span class="text-xs text-gray-400">Supplier Portal</span>
        </div>
       <div class="flex items-center gap-4 text-sm text-gray-500">
    <a href="{{ route('rfqs.index') }}" class="hover:text-gray-900 transition">RFQ Tracker</a>
    <a href="{{ route('agencies.index') }}" class="hover:text-gray-900 transition">Agencies</a>
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