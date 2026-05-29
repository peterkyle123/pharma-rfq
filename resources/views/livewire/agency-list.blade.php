<div>
  @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
        {{ session('message') }}
    </div>
@endif
@if (session()->has('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        {{ session('error') }}
    </div>
@endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Procuring Entity</h1>
            <p class="text-sm text-gray-500 mt-0.5">RFQ Monitoring for Small Value Procurement and Direct Acqusition</p>
        </div>
        <a href="{{ route('agencies.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add Agency
        </a>
    </div>

    <div class="relative mb-4">
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="Search agencies..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
   <table class="w-full text-sm" style="table-layout: fixed">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-48">
                        <button wire:click="sortColumn('name')" class="flex items-center gap-1 hover:text-gray-900">
                            Agency Name {{ $sortBy === 'name' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-32">
                        <button wire:click="sortColumn('type')" class="flex items-center gap-1 hover:text-gray-900 whitespace-nowrap">
                            Type {{ $sortBy === 'type' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-24">
                        <button wire:click="sortColumn('region')" class="flex items-center gap-1 hover:text-gray-900 whitespace-nowrap">
                            Region {{ $sortBy === 'region' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-32">
                        Contact
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-20">
                        <button wire:click="sortColumn('rfqs_count')" class="flex items-center gap-1 hover:text-gray-900 whitespace-nowrap">
                            Total RFQs {{ $sortBy === 'rfqs_count' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-20">
                        <button wire:click="sortColumn('received_count')" class="flex items-center gap-1 hover:text-gray-900 whitespace-nowrap">
                            Received {{ $sortBy === 'received_count' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-24">
                        <button wire:click="sortColumn('reviewing_count')" class="flex items-center gap-1 hover:text-gray-900 whitespace-nowrap">
                            Reviewing {{ $sortBy === 'reviewing_count' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-20">
                        <button wire:click="sortColumn('quoted_count')" class="flex items-center gap-1 hover:text-gray-900 whitespace-nowrap">
                            Quoted {{ $sortBy === 'quoted_count' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-20">
                        <button wire:click="sortColumn('awarded_count')" class="flex items-center gap-1 hover:text-gray-900 whitespace-nowrap">
                            Awarded {{ $sortBy === 'awarded_count' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 w-16">
                        <button wire:click="sortColumn('lost_count')" class="flex items-center gap-1 hover:text-gray-900 whitespace-nowrap">
                            Lost {{ $sortBy === 'lost_count' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="px-4 py-3 w-32"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($agencies as $agency)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-900" style="overflow-wrap: anywhere">
                            {{ $agency->name }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $agency->type }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $agency->region ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            <p>{{ $agency->contact_person ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $agency->contact_email ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $agency->rfqs_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $agency->received_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-amber-50 text-amber-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $agency->reviewing_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-green-50 text-green-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $agency->quoted_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-teal-50 text-teal-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $agency->awarded_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-red-50 text-red-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $agency->lost_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('agencies.edit', $agency) }}"
                                   class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 text-gray-500 hover:text-gray-900 hover:border-gray-400 transition">
                                    Edit
                                </a>
                                <button wire:click="delete({{ $agency->id }})"
                                        wire:confirm="Are you sure you want to delete {{ $agency->name }}?"
                                        class="text-xs border border-red-200 rounded-lg px-3 py-1.5 text-red-500 hover:text-red-700 hover:border-red-400 transition">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-12 text-center text-sm text-gray-400">
                            No agencies found. Add your first one!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($agencies->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $agencies->links() }}
            </div>
        @endif
    </div>
</div>