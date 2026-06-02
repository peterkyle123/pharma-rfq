<!DOCTYPE html>
<html lang="en" id="app-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharma RFQ — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        const theme = localStorage.getItem('theme') || 'light';
        if (theme === 'dark') document.documentElement.classList.add('dark');
        if (theme === 'prime') document.documentElement.classList.add('prime');
    </script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen dark:bg-[#0a0a0a] dark:text-gray-100 prime:bg-white prime:text-gray-900"
      x-data="{
          theme: localStorage.getItem('theme') || 'light',
          cycle() {
              if (this.theme === 'light') this.theme = 'dark';
              else if (this.theme === 'dark') this.theme = 'prime';
              else this.theme = 'light';
              localStorage.setItem('theme', this.theme);
              document.documentElement.classList.remove('dark', 'prime');
              if (this.theme === 'dark') document.documentElement.classList.add('dark');
              if (this.theme === 'prime') document.documentElement.classList.add('prime');
          }
      }">

    {{-- Navbar --}}
    <nav class="bg-white dark:bg-[#111111] prime:bg-white border-b border-gray-200 dark:border-red-900 prime:border-green-200 px-6 py-0 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between h-14">

            {{-- Logo --}}
            <a href="{{ route('rfqs.index') }}" class="flex items-center gap-2.5 font-bold text-base">
                <div class="bg-gray-900 dark:bg-red-800 prime:bg-green-600 text-white rounded-lg w-7 h-7 flex items-center justify-center text-sm">💊</div>
                <span class="text-gray-900 dark:text-red-400 prime:text-green-700">RFQTracker</span>
            </a>

            {{-- Nav links --}}
            <div class="flex items-center gap-1 text-sm">
                <a href="{{ route('rfqs.index') }}"
                   class="px-4 py-2 rounded-lg transition font-medium
                       {{ request()->is('rfqs*')
                           ? 'bg-gray-900 text-white dark:bg-red-900 dark:text-gray-100 prime:bg-green-600 prime:text-white'
                           : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-[#2a2a2a] prime:text-gray-600 prime:hover:text-gray-900 prime:hover:bg-green-50' }}">
                    RFQ's
                </a>
                <a href="{{ route('agencies.index') }}"
                   class="px-4 py-2 rounded-lg transition font-medium
                       {{ request()->is('agencies*')
                           ? 'bg-gray-900 text-white dark:bg-red-900 dark:text-gray-100 prime:bg-green-600 prime:text-white'
                           : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-[#2a2a2a] prime:text-gray-600 prime:hover:text-gray-900 prime:hover:bg-green-50' }}">
                    Agencies
                </a>

                {{-- Theme toggle --}}
                <button @click="cycle()"
                        class="ml-2 p-2 rounded-lg transition text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-[#2a2a2a] prime:text-green-700 prime:hover:bg-green-50"
                        :title="theme === 'light' ? 'Switch to Dark' : theme === 'dark' ? 'Switch to Prime Link' : 'Switch to Light'">
                    {{-- Light mode icon: moon --}}
                    <svg x-show="theme === 'light'" style="display:none" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                    {{-- Dark mode icon: sun --}}
                    <svg x-show="theme === 'dark'" style="display:none" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-9h-1M4.34 12h-1m15.07-6.07l-.71.71M6.34 17.66l-.71.71m12.02 0l-.71-.71M6.34 6.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
                    </svg>
                    {{-- Prime mode icon: heart --}}
                    <svg x-show="theme === 'prime'" style="display:none" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </div>

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