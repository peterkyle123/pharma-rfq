<div>
@if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 rounded-lg bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-400">
        {{ session('message') }}
    </div>
@endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">RFQ Tracker</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">For Small Value Procurement and Direct Acquisition</p>
        </div>
        <a href="{{ route('rfqs.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add RFQ
        </a>
    </div>

    {{-- Metrics --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 dark:bg-[#1a1a1a] dark:border dark:border-red-900 rounded-xl p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total RFQs</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $metrics['total'] }}</p>
        </div>
        <div class="bg-amber-50 dark:bg-[#1a1a1a] dark:border dark:border-red-900 rounded-xl p-4">
            <p class="text-xs text-amber-700 dark:text-red-400 mb-1">Pending action</p>
            <p class="text-2xl font-semibold text-amber-800 dark:text-red-400">{{ $metrics['pending'] }}</p>
        </div>
        <div class="bg-green-50 dark:bg-[#1a1a1a] dark:border dark:border-red-900 rounded-xl p-4">
            <p class="text-xs text-green-700 dark:text-gray-400 mb-1">Quoted</p>
            <p class="text-2xl font-semibold text-green-800 dark:text-gray-100">{{ $metrics['quoted'] }}</p>
        </div>
        <div class="bg-blue-50 dark:bg-[#1a1a1a] dark:border dark:border-red-900 rounded-xl p-4">
            <p class="text-xs text-blue-700 dark:text-gray-400 mb-1">Awarded</p>
            <p class="text-2xl font-semibold text-blue-800 dark:text-gray-100">{{ $metrics['awarded'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[200px]">
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Search agency or RFQ no..."
                   class="w-full pl-4 pr-4 py-2 text-sm border border-gray-200 dark:border-[#2a2a2a] dark:bg-[#1a1a1a] dark:text-gray-100 dark:placeholder-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
        @foreach (['all', 'Received', 'Reviewing', 'Quoted', 'Awarded', 'Lost'] as $tab)
            <button wire:click="setStatus('{{ $tab }}')"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium border transition
                        {{ $status === $tab
                            ? 'bg-white dark:bg-red-900 border-gray-300 dark:border-red-700 text-gray-900 dark:text-gray-100 shadow-sm'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-[#2a2a2a]' }}">
                {{ ucfirst($tab) }}
            </button>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-[#111111] rounded-xl border border-gray-200 dark:border-red-900 overflow-hidden">
        <table class="w-full text-sm table-fixed" style="table-layout: fixed">
            <thead class="bg-gray-50 dark:bg-[#1a1a1a] border-b border-gray-200 dark:border-red-900">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-28">
                        <button wire:click="sortColumn('rfq_number')" class="flex items-center gap-1 hover:text-gray-900 dark:hover:text-gray-100 whitespace-nowrap">
                            RFQ no. {{ $sortBy === 'rfq_number' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-44">
                        <button wire:click="sortColumn('agency_id')" class="flex items-center gap-1 hover:text-gray-900 dark:hover:text-gray-100 whitespace-nowrap">
                            Agency {{ $sortBy === 'agency_id' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-28">
                        <button wire:click="sortColumn('abc')" class="flex items-center gap-1 hover:text-gray-900 dark:hover:text-gray-100 whitespace-nowrap">
                            ABC (₱) {{ $sortBy === 'abc' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-32">
                        <button wire:click="sortColumn('deadline')" class="flex items-center gap-1 hover:text-gray-900 dark:hover:text-gray-100 whitespace-nowrap">
                            Deadline {{ $sortBy === 'deadline' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-36">
                        <button wire:click="sortColumn('date_received')" class="flex items-center gap-1 hover:text-gray-900 dark:hover:text-gray-100 whitespace-nowrap">
                            Days Since Received {{ $sortBy === 'date_received' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-28">
                        <button wire:click="sortColumn('status')" class="flex items-center gap-1 hover:text-gray-900 dark:hover:text-gray-100 whitespace-nowrap">
                            Status {{ $sortBy === 'status' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 w-32">
                        <button wire:click="sortColumn('total_quoted')" class="flex items-center gap-1 hover:text-gray-900 dark:hover:text-gray-100 whitespace-nowrap">
                            Quoted Price {{ $sortBy === 'total_quoted' ? ($sortDir === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </th>
                    <th class="px-4 py-3 w-24"></th>
                </tr>
            </thead>

            @forelse ($rfqs as $rfq)
                <tbody wire:key="rfq-{{ $rfq->id }}" class="{{ $loop->even ? 'bg-gray-50 dark:bg-[#161616]' : 'bg-white dark:bg-[#111111]' }}">

                    {{-- Main row --}}
                    <tr class="hover:bg-gray-100 dark:hover:bg-[#2a2a2a] transition cursor-pointer"
                        wire:click="toggleOpen({{ $rfq->id }})">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $rfq->rfq_number }}</td>
                        <td class="px-4 py-3 truncate">
                            <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $rfq->agency->name }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $rfq->agency->type }}</p>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ $rfq->abc ? '₱' . number_format($rfq->abc, 0) : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php $days = $rfq->deadline ? $rfq->days_left : null; @endphp
                            <span class="text-sm {{ $days === null ? 'text-gray-400' : ($days < 0 ? 'text-red-600' : ($days <= 1 ? 'text-red-500' : ($days <= 3 ? 'text-amber-600' : 'text-gray-500 dark:text-gray-400'))) }}">
                                {{ $days === null ? '—' : ($days < 0 ? 'Overdue' : ($days === 0 ? 'Today' : ($days === 1 ? '1 day left' : $days . ' days left'))) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            {{ $rfq->days_since_received }} {{ $rfq->days_since_received === 1 ? 'day' : 'days' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                            $colors = [
                                'Received'  => 'bg-blue-50 text-blue-800 dark:bg-blue-950 dark:text-blue-300',
                                'Reviewing' => 'bg-amber-50 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
                                'Quoted'    => 'bg-green-50 text-green-800 dark:bg-green-950 dark:text-green-300',
                                'Awarded'   => 'bg-teal-50 text-teal-800 dark:bg-teal-950 dark:text-teal-300',
                                'Lost'      => 'bg-red-50 text-red-800 dark:bg-red-950 dark:text-red-400',
                            ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $colors[$rfq->status] ?? '' }}">
                                {{ $rfq->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ $rfq->total_quoted > 0 ? '₱' . number_format($rfq->total_quoted, 2) : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right" @click.stop x-data>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('rfqs.show', $rfq) }}"
                                   class="text-xs border border-gray-200 dark:border-[#2a2a2a] dark:text-gray-400 dark:hover:text-gray-100 dark:hover:border-red-700 rounded-lg px-3 py-1.5 text-gray-500 hover:text-gray-900 hover:border-gray-400 transition">
                                    View
                                </a>
                            </div>
                        </td>
                    </tr>

                    {{-- Hint row --}}
                    @if(!in_array($rfq->id, $openRows))
                    <tr wire:click="toggleOpen({{ $rfq->id }})" class="cursor-pointer">
                        <td colspan="8" class="text-center text-xs text-gray-400 dark:text-gray-600 italic pb-2 {{ $loop->even ? 'bg-gray-50 dark:bg-[#161616]' : 'bg-white dark:bg-[#111111]' }}">
                            click row to view attachments
                        </td>
                    </tr>
                    @endif

                    {{-- Document checklist dropdown --}}
                    @if(in_array($rfq->id, $openRows))
                    <tr>
                        <td colspan="8" class="px-6 py-4 {{ $loop->even ? 'bg-gray-50 dark:bg-[#161616]' : 'bg-white dark:bg-[#111111]' }}">
                            <p class="text-xs font-medium text-gray-400 dark:text-red-700 uppercase tracking-wide mb-3">Documents on hand</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @php
                                $docs = [
                                    'rfq_form'        => 'Request for Quotation Form',
                                    'notice_of_award' => 'Notice of Award',
                                    'purchase_order'  => 'Purchase Order',
                                    'ntp'             => 'NTP',
                                ];
                                $rfqDocs = $rfq->documents ?? [];
                                @endphp
                                @foreach ($docs as $key => $label)
                                    @php
                                        $docData   = $rfqDocs[$key] ?? false;
                                        $isChecked = is_array($docData) ? !empty($docData['received']) : (bool) $docData;
                                        $docDate   = is_array($docData) ? ($docData['date'] ?? '') : '';
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <label class="flex items-center gap-2 cursor-pointer select-none group">
                                            <input type="checkbox"
                                                   @checked($isChecked)
                                                   @disabled(in_array($key, ['notice_of_award', 'ntp']) && $rfq->status === 'Lost')
                                                   wire:click.stop="toggleDoc({{ $rfq->id }}, '{{ $key }}')"
                                                   class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">{{ $label }}</span>
                                        </label>
                                        @if($isChecked)
                                            <div class="ml-6 flex items-center gap-2">
                                                <label class="text-xs text-gray-400 dark:text-gray-500">Date received:</label>
                                                <input type="date"
                                                       value="{{ $docDate }}"
                                                       x-on:change="$wire.setDocDate({{ $rfq->id }}, '{{ $key }}', $event.target.value)"
                                                       class="text-xs border border-gray-200 dark:border-[#2a2a2a] dark:bg-[#1a1a1a] dark:text-gray-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endif

                </tbody>
            @empty
                <tbody>
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-400 dark:text-gray-600">
                            No RFQs found. Add your first one!
                        </td>
                    </tr>
                </tbody>
            @endforelse

        </table>
        @if ($rfqs->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-red-900">
                {{ $rfqs->links() }}
            </div>
        @endif
    </div>
</div>