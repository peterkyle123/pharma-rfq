
<div>
@if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
        {{ session('message') }}
    </div>
@endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">RFQ Tracker</h1>
            <p class="text-sm text-gray-500 mt-0.5">Incoming government RFQs — supplier view</p>
        </div>
        <a href="{{ route('rfqs.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add RFQ
        </a>
    </div>

    {{-- Metrics --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Total RFQs</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $metrics['total'] }}</p>
        </div>
        <div class="bg-amber-50 rounded-xl p-4">
            <p class="text-xs text-amber-700 mb-1">Pending action</p>
            <p class="text-2xl font-semibold text-amber-800">{{ $metrics['pending'] }}</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4">
            <p class="text-xs text-green-700 mb-1">Quoted</p>
            <p class="text-2xl font-semibold text-green-800">{{ $metrics['quoted'] }}</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4">
            <p class="text-xs text-blue-700 mb-1">Win rate</p>
            <p class="text-2xl font-semibold text-blue-800">{{ $metrics['win_rate'] }}%</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px]">
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Search agency or RFQ no..."
                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        @foreach (['all', 'Received', 'Reviewing', 'Quoted', 'Awarded', 'Lost'] as $tab)
            <button wire:click="setStatus('{{ $tab }}')"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium border transition
                        {{ $status === $tab
                            ? 'bg-white border-gray-300 text-gray-900 shadow-sm'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                {{ ucfirst($tab) }}
            </button>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">
                        <button wire:click="sortBy('rfq_number')">
                            RFQ no. {{ $sortBy === 'rfq_number' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Agency</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Items</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">
                        <button wire:click="sortBy('abc')">
                            ABC (₱) {{ $sortBy === 'abc' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">
                        <button wire:click="sortBy('deadline')">
                            Deadline {{ $sortBy === 'deadline' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rfqs as $rfq)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $rfq->rfq_number }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $rfq->agency->name }}</p>
                            <p class="text-xs text-gray-400">{{ $rfq->agency->type }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500 max-w-[180px] truncate">
                            {{ $rfq->items->pluck('item_description')->implode(', ') ?: '—' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $rfq->abc ? '₱' . number_format($rfq->abc, 0) : '—' }}
                        </td>
                        <td class="px-4 py-3">
                           @php $days = (int) round(now()->startOfDay()->diffInDays($rfq->deadline->startOfDay(), false)); @endphp
                            <span class="text-sm {{ $days < 0 ? 'text-red-600' : ($days <= 1 ? 'text-red-500' : ($days <= 3 ? 'text-amber-600' : 'text-gray-500')) }}">
                                {{ $days < 0 ? 'Overdue' : ($days === 0 ? 'Today' : ($days === 1 ? '1 day left' : $days . ' days left')) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                            $colors = [
                                'Received'  => 'bg-blue-50 text-blue-800',
                                'Reviewing' => 'bg-amber-50 text-amber-800',
                                'Quoted'    => 'bg-green-50 text-green-800',
                                'Awarded'   => 'bg-teal-50 text-teal-800',
                                'Lost'      => 'bg-red-50 text-red-800',
                            ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$rfq->status] ?? '' }}">
                                {{ $rfq->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('rfqs.show', $rfq) }}"
                               class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 text-gray-500 hover:text-gray-900 hover:border-gray-400 transition">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">
                            No RFQs found. Add your first one!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($rfqs->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $rfqs->links() }}
            </div>
        @endif
    </div>
</div>
