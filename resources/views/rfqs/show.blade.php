@extends('layouts.app')

@section('content')
<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="font-mono text-xs text-gray-400 mb-1">{{ $rfq->rfq_number }}</p>
            <h1 class="text-xl font-semibold text-gray-900">{{ $rfq->agency->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $rfq->agency->type }} · {{ $rfq->agency->region }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('rfqs.edit', $rfq) }}"
               class="text-sm border border-gray-200 px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:border-gray-400 transition">
                Edit
            </a>
            <a href="{{ route('rfqs.index') }}"
               class="text-sm border border-gray-200 px-4 py-2 rounded-lg text-gray-500 hover:text-gray-900 hover:border-gray-400 transition">
                ← Back
            </a>
        </div>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-4">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-4">RFQ Details</p>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-gray-400 mb-1">Status</p>
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
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Date Received</p>
                <p class="text-sm font-medium text-gray-900">{{ $rfq->date_received->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Deadline</p>
                @php $days = $rfq->days_left; @endphp
                <p class="text-sm font-medium {{ $days < 0 ? 'text-red-600' : ($days <= 3 ? 'text-amber-600' : 'text-gray-900') }}">
                    {{ $rfq->deadline->format('M d, Y') }}
                    <span class="font-normal text-xs">
                        ({{ $days < 0 ? 'Overdue' : ($days === 0 ? 'Today' : $days . ' days left') }})
                    </span>
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">ABC</p>
                <p class="text-sm font-medium text-gray-900">
                    {{ $rfq->abc ? '₱' . number_format($rfq->abc, 2) : '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">PhilGEPS Ref</p>
                <p class="text-sm font-medium text-gray-900 font-mono">{{ $rfq->philgeps_ref ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Procurement Mode</p>
                <p class="text-sm font-medium text-gray-900">Small Value Procurement</p>
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Line Items</p>
            <p class="text-xs text-gray-400">{{ $rfq->items->count() }} item(s)</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Item Description</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Unit</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Qty</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Unit Price (₱)</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Total (₱)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rfq->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $item->item_description }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $item->unit }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ number_format($item->quantity) }}</td>
                        <td class="px-6 py-3 text-gray-900">
                            {{ $item->unit_price ? '₱' . number_format($item->unit_price, 2) : '—' }}
                        </td>
                        <td class="px-6 py-3 font-medium text-gray-900">
                            {{ $item->total_price ? '₱' . number_format($item->total_price, 2) : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">
                            No items added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($rfq->items->count() > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Total Quoted</td>
                        <td class="px-6 py-3 font-semibold text-gray-900">
                            ₱{{ number_format($rfq->total_quoted, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500">ABC Remaining</td>
                        @php $remaining = $rfq->abc - $rfq->total_quoted; @endphp
                        <td class="px-6 py-3 font-semibold {{ $remaining < 0 ? 'text-red-600' : 'text-green-700' }}">
                            ₱{{ number_format(abs($remaining), 2) }}
                            {{ $remaining < 0 ? '(over budget)' : '' }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    {{-- Notes --}}
    @if ($rfq->notes)
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Notes</p>
            <p class="text-sm text-gray-700">{{ $rfq->notes }}</p>
        </div>
    @endif

    {{-- Danger zone --}}
    <div class="bg-white rounded-xl border border-red-100 p-6">
        <p class="text-xs font-medium text-red-400 uppercase tracking-wide mb-3">Danger Zone</p>
        <form action="{{ route('rfqs.destroy', $rfq) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete {{ $rfq->rfq_number }}?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="text-sm text-red-600 border border-red-200 px-4 py-2 rounded-lg hover:bg-red-50 transition">
                Delete this RFQ
            </button>
        </form>
    </div>

</div>
@endsection