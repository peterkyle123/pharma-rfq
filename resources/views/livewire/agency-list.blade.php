<div>
  @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
        {{ session('message') }}
    </div>
@endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Agencies</h1>
            <p class="text-sm text-gray-500 mt-0.5">Government agencies you receive RFQs from</p>
        </div>
        <a href="{{ route('agencies.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add Agencya
        </a>
    </div>

    <div class="relative mb-4">
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="Search agencies..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Agency Name</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Region</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">Contact</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500">RFQs</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($agencies as $agency)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $agency->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $agency->type }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $agency->region ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            <p>{{ $agency->contact_person ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $agency->contact_email ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-blue-50 text-blue-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $agency->rfqs_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
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
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-400">
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