<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $rfqId ? 'Edit RFQ' : 'Add New RFQ' }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $rfqId ? 'Update the details of this RFQ' : 'Fill in the details from the government agency\'s RFQ document' }}</p>
        </div>
        <a href="{{ route('rfqs.index') }}"
           class="text-sm text-gray-500 hover:text-gray-900 border border-gray-200 px-4 py-2 rounded-lg transition">
            ← Back to tracker
        </a>
    </div>

    <form wire:submit.prevent="save">

        {{-- RFQ Information --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-4">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-4">RFQ Information</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        RFQ Number <span class="text-gray-400 font-normal">(leave blank to auto-generate)</span>
                    </label>
                    <input type="text" wire:model="rfq_number"
                           placeholder="e.g. RFQ-2025-001"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('rfq_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agency <span class="text-red-500">*</span></label>
                    <select wire:model="agency_id"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select agency...</option>
                        @foreach ($agencies as $agency)
                            <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                        @endforeach
                    </select>
                    @error('agency_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Received <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="date_received"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('date_received') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deadline <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="deadline"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        ABC (₱) <span class="text-gray-400 font-normal">Approved Budget for Contract</span>
                    </label>
                    <input type="number" wire:model="abc"
                           placeholder="0.00" step="0.01" min="0"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('abc') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select wire:model="status"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach (['Received', 'Reviewing', 'Quoted', 'Awarded', 'Lost'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PhilGEPS Reference No.</label>
                    <input type="text" wire:model="philgeps_ref"
                           placeholder="e.g. 1234567"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('philgeps_ref') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- Line Items --}}
        <div class="bg-white rounded-xl border border-gray-200 mb-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                    Line Items
                    <span class="ml-2 bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">
                        {{ count($filteredItems) }}{{ count($filteredItems) !== $totalItemCount ? ' of ' . $totalItemCount : '' }}
                    </span>
                </p>
                <button type="button" wire:click="addItem"
                        class="text-xs text-blue-600 hover:text-blue-800 border border-blue-200 px-3 py-1.5 rounded-lg transition">
                    + Add Item
                </button>
        </div>
    @error('items_empty')
        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
    @enderror
</div>

            {{-- Search bar --}}
            <div class="px-6 py-3 border-b border-gray-100">
                <input wire:model.live.debounce.300ms="itemSearch"
                       type="text"
                       placeholder="Search by description or unit..."
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">#</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Item Description</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Unit</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Qty</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Unit Price (₱)</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Total (₱)</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pagedItems as $index => $item)
                        <tr wire:key="item-{{ $index }}-{{ $item['item_description'] }}-{{ $item['unit'] }}" class="border-t border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-400 text-xs">{{ ($itemPage - 1) * $itemsPerPage + $loop->iteration }}</td>
                            <td class="px-4 py-2">
                                <input type="text" wire:model="items.{{ $index }}.item_description"
                                       placeholder="e.g. Amoxicillin 500mg Capsule"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error("items.{$index}.item_description") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" wire:model="items.{{ $index }}.unit"
                                       placeholder="tablet"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error("items.{$index}.unit") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" wire:model="items.{{ $index }}.quantity"
                                       placeholder="0" min="1"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error("items.{$index}.quantity") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" wire:model="items.{{ $index }}.unit_price"
                                       placeholder="0.00" step="0.01" min="0"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error("items.{$index}.unit_price") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-2 text-gray-500 text-sm">
                                @php
                                    $total = ($item['unit_price'] && $item['quantity'])
                                        ? number_format((float)$item['unit_price'] * (float)$item['quantity'], 2)
                                        : null;
                                @endphp
                                {{ $total ? '₱' . $total : '—' }}
                            </td>
                            <td class="px-4 py-2">
                                @if ($totalItemCount > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                            class="text-red-400 hover:text-red-600 text-xs border border-red-200 rounded-lg px-2 py-1 transition">
                                        Remove
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">
                                @if ($itemSearch)
                                    No items match "<span class="font-medium">{{ $itemSearch }}</span>".
                                @else
                                    No items added yet.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($totalItemPages > 1)
                <div class="px-6 py-3 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
                    <span class="text-xs">Page {{ $itemPage }} of {{ $totalItemPages }}</span>
                    <div class="flex gap-2">
                        <button type="button" wire:click="itemPrevPage"
                                @disabled($itemPage <= 1)
                                class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            ← Prev
                        </button>
                        <button type="button" wire:click="itemNextPage"
                                @disabled($itemPage >= $totalItemPages)
                                class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            Next →
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-4">Internal Notes</p>
            <textarea wire:model="notes" rows="3"
                      placeholder="Add any internal notes about this RFQ..."
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                {{ $rfqId ? 'Update RFQ' : 'Save RFQ' }}
            </button>
            <a href="{{ route('rfqs.index') }}"
               class="text-sm text-gray-500 hover:text-gray-900 transition">
                Cancel
            </a>
        </div>

    </form>
</div>