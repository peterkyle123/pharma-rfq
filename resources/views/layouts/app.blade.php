<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharma RFQ — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> -->
<script>if(localStorage.getItem('theme')==='dark')document.documentElement.classList.add('dark')</script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen dark:bg-[#0d1117] dark:text-gray-100"
      x-data="{ dark: localStorage.getItem('theme') === 'dark' }"
      x-init="$watch('dark', v => { localStorage.setItem('theme', v ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', v) }); document.documentElement.classList.toggle('dark', dark)">

    {{-- Navbar --}}
    <nav class="bg-white dark:bg-[#111111] border-b border-gray-100 dark:border-red-900 px-6 py-0 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto flex items-center justify-between h-14">

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
                    RFQ's
                </a>
                <a href="{{ route('agencies.index') }}"
                   class="px-4 py-2 rounded-lg transition font-medium
                       {{ request()->is('agencies*') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Agencies
                </a>
    </div>

            {{-- Dark mode toggle --}}
            <button @click="dark = !dark"
                    class="ml-2 p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-gray-700 transition">
                <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
                <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-9h-1M4.34 12h-1m15.07-6.07l-.71.71M6.34 17.66l-.71.71m12.02 0l-.71-.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
                </svg>
            </button>

        </div>
    </nav>
    {{-- Main content --}}
    <main class="max-w-7xl mx-auto px-6 py-8">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    @livewireScripts
</body>
</html>