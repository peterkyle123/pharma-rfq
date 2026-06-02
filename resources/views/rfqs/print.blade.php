<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $rfq->rfq_number }} — Print Summary</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 p-8 max-w-4xl mx-auto">

    {{-- Print button --}}
<div class="no-print flex justify-end gap-2 mb-6">
        <a href="{{ route('rfqs.index') }}"
           class="border border-gray-300 hover:border-gray-400 text-gray-500 hover:text-gray-900 text-sm font-medium px-4 py-2 rounded-lg transition">
            ← Back
        </a>
        <button onclick="window.print()"
                class="bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            🖨 Print
        </button>
    </div>

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6 pb-4 border-b border-gray-300">
        <div>
            <p class="text-xs text-gray-400 font-mono mb-1">{{ $rfq->rfq_number }}</p>
            <h1 class="text-2xl font-bold text-gray-900">{{ $rfq->agency->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $rfq->agency->type }} · {{ $rfq->agency->region ?? '—' }}</p>
        </div>
        <div class="text-right">
            @php
            $colors = [
                'Received'  => 'bg-blue-50 text-blue-800',
                'Reviewing' => 'bg-amber-50 text-amber-800',
                'Quoted'    => 'bg-green-50 text-green-800',
                'Awarded'   => 'bg-teal-50 text-teal-800',
                'Lost'      => 'bg-red-50 text-red-800',
            ];
            @endphp
            <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ $colors[$rfq->status] ?? '' }}">
                {{ $rfq->status }}
            </span>
            <p class="text-xs text-gray-400 mt-2">Printed: {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>

    {{-- RFQ Details --}}
    <div class="mb-6">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-3">RFQ Details</p>
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <p class="text-xs text-gray-400 mb-1">Date Received</p>
                <p class="text-sm font-medium text-gray-900">{{ $rfq->date_received->format('M d, Y') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <p class="text-xs text-gray-400 mb-1">Deadline</p>
                <p class="text-sm font-medium text-gray-900">
                    {{ $rfq->deadline ? $rfq->deadline->format('M d, Y') : '—' }}
                </p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <p class="text-xs text-gray-400 mb-1">ABC (₱)</p>
                <p class="text-sm font-medium text-gray-900">{{ $rfq->abc ? '₱' . number_format($rfq->abc, 2) : '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <p class="text-xs text-gray-400 mb-1">PhilGEPS Ref</p>
                <p class="text-sm font-medium text-gray-900 font-mono">{{ $rfq->philgeps_ref ?? '—' }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <p class="text-xs text-gray-400 mb-1">Procurement Mode</p>
                <p class="text-sm font-medium text-gray-900">Small Value Procurement</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <p class="text-xs text-gray-400 mb-1">Total Quoted</p>
                <p class="text-sm font-medium text-gray-900">{{ $rfq->total_quoted > 0 ? '₱' . number_format($rfq->total_quoted, 2) : '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="mb-6">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-3">Line Items</p>
        <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-2 text-xs font-medium text-gray-500">#</th>
                    <th class="text-left px-4 py-2 text-xs font-medium text-gray-500">Description</th>
                    <th class="text-left px-4 py-2 text-xs font-medium text-gray-500">Unit</th>
                    <th class="text-left px-4 py-2 text-xs font-medium text-gray-500">Qty</th>
                    <th class="text-left px-4 py-2 text-xs font-medium text-gray-500">Unit Price</th>
                    <th class="text-left px-4 py-2 text-xs font-medium text-gray-500">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rfq->items as $i => $item)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-2 text-xs text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 text-gray-900">{{ $item->item_description }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $item->unit }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ number_format($item->quantity) }}</td>
                        <td class="px-4 py-2 text-gray-900">{{ $item->unit_price ? '₱' . number_format($item->unit_price, 2) : '—' }}</td>
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $item->total_price ? '₱' . number_format($item->total_price, 2) : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">No items.</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($rfq->items->count() > 0)
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-right text-sm font-medium text-gray-500">Total Quoted</td>
                        <td class="px-4 py-2 font-bold text-gray-900">₱{{ number_format($rfq->total_quoted, 2) }}</td>
                    </tr>
                    @if ($rfq->abc)
                        @php $remaining = $rfq->abc - $rfq->total_quoted; @endphp
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-right text-sm font-medium text-gray-500">ABC Remaining</td>
                            <td class="px-4 py-2 font-bold {{ $remaining < 0 ? 'text-red-600' : 'text-green-700' }}">
                                ₱{{ number_format(abs($remaining), 2) }} {{ $remaining < 0 ? '(over budget)' : '' }}
                            </td>
                        </tr>
                    @endif
                </tfoot>
            @endif
        </table>
    </div>

    {{-- Documents on Hand --}}
    <div class="mb-6">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-3">Documents on Hand</p>
        <div class="grid grid-cols-2 gap-3">
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
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
                    <span class="{{ $isChecked ? 'text-green-600' : 'text-gray-300' }} text-lg">{{ $isChecked ? '✓' : '○' }}</span>
                    <div>
                        <p class="text-sm font-medium {{ $isChecked ? 'text-gray-900' : 'text-gray-400' }}">{{ $label }}</p>
                        @if ($isChecked && $docDate)
                            <p class="text-xs text-gray-400">Received: {{ \Carbon\Carbon::parse($docDate)->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Notes --}}
    @if ($rfq->notes)
        <div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Notes</p>
            <p class="text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg p-4">{{ $rfq->notes }}</p>
        </div>
    @endif

</body>
</html>