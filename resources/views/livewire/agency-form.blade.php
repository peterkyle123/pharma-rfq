<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100 prime:text-gray-900">{{ $agencyId ? 'Edit Agency' : 'Add Agency' }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 prime:text-gray-500 mt-0.5">{{ $agencyId ? 'Update agency details' : 'Add a new government agency' }}</p>
        </div>
        <a href="{{ route('agencies.index') }}"
           class="text-sm text-gray-500 dark:text-gray-400 prime:text-gray-500 hover:text-gray-900 dark:hover:text-gray-100 prime:hover:text-gray-900 border border-gray-200 dark:border-[#2a2a2a] prime:border-gray-200 dark:hover:border-red-700 prime:hover:border-green-400 px-4 py-2 rounded-lg transition">
            ← Back
        </a>
    </div>

    <form wire:submit.prevent="save">
       <div class="bg-white dark:bg-[#111111] prime:bg-white rounded-xl border border-gray-200 dark:border-red-900 prime:border-green-200 p-6 mb-6">
            <p class="text-xs font-medium text-gray-400 dark:text-red-700 prime:text-green-700 uppercase tracking-wide mb-4">Agency Information</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 prime:text-gray-700 mb-1">Agency Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name"
                           placeholder="e.g. Quezon City General Hospital"
                           class="w-full border border-gray-200 dark:border-[#2a2a2a] dark:bg-[#1a1a1a] dark:text-gray-100 dark:placeholder-gray-500 prime:border-gray-200 prime:bg-white prime:text-gray-900 prime:placeholder-gray-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-red-500 prime:focus:ring-green-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 prime:text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select wire:model.live="type"
                            class="w-full border border-gray-200 dark:border-[#2a2a2a] dark:bg-[#1a1a1a] dark:text-gray-100 prime:border-gray-200 prime:bg-white prime:text-gray-900 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-red-500 prime:focus:ring-green-500">
                        @foreach (['Government Hospital', 'LGU', 'National Agency', 'SUC', 'GOCC', 'Other'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                    @if($type === 'Other')
                        <input type="text" wire:model="customType"
                            placeholder="Please specify..."
                            class="mt-2 w-full border border-gray-200 dark:border-[#2a2a2a] dark:bg-[#1a1a1a] dark:text-gray-100 dark:placeholder-gray-500 prime:border-gray-200 prime:bg-white prime:text-gray-900 prime:placeholder-gray-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-red-500 prime:focus:ring-green-500">
                        @error('customType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @endif
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 prime:text-gray-700 mb-1">Region</label>
                    <select wire:model="region"
                            class="w-full border border-gray-200 dark:border-[#2a2a2a] dark:bg-[#1a1a1a] dark:text-gray-100 prime:border-gray-200 prime:bg-white prime:text-gray-900 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-red-500 prime:focus:ring-green-500">
                        <option value="">Select region...</option>
                        @foreach (['NCR', 'CAR', 'Region I', 'Region II', 'Region III', 'Region IV-A', 'Region IV-B', 'Region V', 'Region VI', 'Region VII', 'Region VIII', 'Region IX', 'Region X', 'Region XI', 'Region XII', 'Region XIII', 'BARMM'] as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                    @error('region') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 prime:text-gray-700 mb-1">Contact Person</label>
                    <input type="text" wire:model="contact_person"
                           placeholder="e.g. Maria Santos"
                           class="w-full border border-gray-200 dark:border-[#2a2a2a] dark:bg-[#1a1a1a] dark:text-gray-100 dark:placeholder-gray-500 prime:border-gray-200 prime:bg-white prime:text-gray-900 prime:placeholder-gray-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-red-500 prime:focus:ring-green-500">
                    @error('contact_person') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 prime:text-gray-700 mb-1">Contact Email</label>
                    <input type="email" wire:model="contact_email"
                           placeholder="e.g. bac@agency.gov.ph"
                           class="w-full border border-gray-200 dark:border-[#2a2a2a] dark:bg-[#1a1a1a] dark:text-gray-100 dark:placeholder-gray-500 prime:border-gray-200 prime:bg-white prime:text-gray-900 prime:placeholder-gray-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-red-500 prime:focus:ring-green-500">
                    @error('contact_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 prime:text-gray-700 mb-1">Contact Phone</label>
                    <input type="text" wire:model="contact_phone"
                           placeholder="e.g. 02-8123-4567"
                           class="w-full border border-gray-200 dark:border-[#2a2a2a] dark:bg-[#1a1a1a] dark:text-gray-100 dark:placeholder-gray-500 prime:border-gray-200 prime:bg-white prime:text-gray-900 prime:placeholder-gray-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-red-500 prime:focus:ring-green-500">
                    @error('contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-gray-900 hover:bg-gray-800 dark:bg-red-600 dark:hover:bg-red-700 prime:bg-green-600 prime:hover:bg-green-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                {{ $agencyId ? 'Update Agency' : 'Save Agency' }}
            </button>
            <a href="{{ route('agencies.index') }}"
               class="text-sm text-gray-500 dark:text-gray-400 prime:text-gray-500 hover:text-gray-900 dark:hover:text-gray-100 prime:hover:text-gray-900 transition">
                Cancel
            </a>
        </div>
    </form>
</div>