<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $agencyId ? 'Edit Agency' : 'Add Agency' }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $agencyId ? 'Update agency details' : 'Add a new government agency' }}</p>
        </div>
        <a href="{{ route('agencies.index') }}"
           class="text-sm text-gray-500 hover:text-gray-900 border border-gray-200 px-4 py-2 rounded-lg transition">
            ← Back
        </a>
    </div>

    <form wire:submit.prevent="save">
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-4">Agency Information</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agency Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name"
                           placeholder="e.g. Quezon City General Hospital"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select wire:model="type"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach (['Government Hospital', 'LGU', 'National Agency', 'SUC', 'GOCC', 'Other'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                    <select wire:model="region"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select region...</option>
                        @foreach (['NCR', 'CAR', 'Region I', 'Region II', 'Region III', 'Region IV-A', 'Region IV-B', 'Region V', 'Region VI', 'Region VII', 'Region VIII', 'Region IX', 'Region X', 'Region XI', 'Region XII', 'Region XIII', 'BARMM'] as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                    @error('region') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                    <input type="text" wire:model="contact_person"
                           placeholder="e.g. Maria Santos"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('contact_person') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" wire:model="contact_email"
                           placeholder="e.g. bac@agency.gov.ph"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('contact_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                    <input type="text" wire:model="contact_phone"
                           placeholder="e.g. 02-8123-4567"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                {{ $agencyId ? 'Update Agency' : 'Save Agency' }}
            </button>
            <a href="{{ route('agencies.index') }}"
               class="text-sm text-gray-500 hover:text-gray-900 transition">
                Cancel
            </a>
        </div>
    </form>
</div>