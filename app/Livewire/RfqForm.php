<?php

namespace App\Livewire;

use App\Models\Rfq;
use App\Models\Agency;
use App\Models\RfqItem;
use Livewire\Component;

class RfqForm extends Component
{
    // -------------------------------------------------------------------------
    // RFQ header fields — bound to the form inputs via wire:model
    // -------------------------------------------------------------------------
    public string $rfq_number    = '';
    public string $agency_id     = '';
    public string $date_received = '';
    public string $deadline      = '';
    public string $abc           = '';
    public string $status        = 'Received';
    public string $notes         = '';
    public string $philgeps_ref  = '';

    // Line items array — each entry is [item_description, unit, quantity, unit_price]
    public array $items = [
        ['item_description' => '', 'unit' => '', 'quantity' => '', 'unit_price' => ''],
    ];

    // null = create mode, set to an ID = edit mode
    public ?int $rfqId = null;

    // -------------------------------------------------------------------------
    // Item search & pagination state
    // -------------------------------------------------------------------------
    public string $itemSearch   = '';
    public int    $itemsPerPage = 5;
    public int    $itemPage     = 1;

    // -------------------------------------------------------------------------
    // Paste items state
    // -------------------------------------------------------------------------
    public bool   $showPasteArea = false;
    public string $pasteText     = '';

    // -------------------------------------------------------------------------
    // Mount — load existing RFQ data when editing, or set defaults when creating
    // -------------------------------------------------------------------------
    public function mount(?int $rfqId = null): void
    {
        if ($rfqId) {
            // Edit mode: populate all fields from the existing RFQ
            $rfq = Rfq::with('items')->findOrFail($rfqId);
            $this->rfqId         = $rfq->id;
            $this->rfq_number    = $rfq->rfq_number;
            $this->agency_id     = (string) $rfq->agency_id;
            $this->date_received = $rfq->date_received->format('Y-m-d');
            // Deadline is nullable — only format if present
            $this->deadline      = $rfq->deadline ? $rfq->deadline->format('Y-m-d') : '';
            $this->abc           = (string) ($rfq->abc ?? '');
            $this->status        = $rfq->status;
            $this->notes         = $rfq->notes ?? '';
            $this->philgeps_ref  = $rfq->philgeps_ref ?? '';

            // array_values() ensures clean 0-based integer keys
            // which is required for wire:model binding to work correctly
            $this->items = array_values($rfq->items->map(fn($i) => [
                'item_description' => $i->item_description,
                'unit'             => $i->unit,
                'quantity'         => (string) $i->quantity,
                'unit_price'       => (string) ($i->unit_price ?? ''),
            ])->toArray());
        } else {
            // Create mode: default date received to today
            $this->date_received = now()->format('Y-m-d');
        }
    }

    // -------------------------------------------------------------------------
    // Add a new blank item row
    // Prevents adding if any existing row still has empty required fields
    // -------------------------------------------------------------------------
    public function addItem(): void
    {
        $hasEmpty = collect($this->items)->some(
            fn($item) => trim($item['item_description'] ?? '') === '' ||
                         trim($item['unit'] ?? '') === '' ||
                         trim($item['quantity'] ?? '') === ''
        );

        if ($hasEmpty) {
            $this->addError('items_empty', 'Please fill in all existing item fields before adding a new one.');
            return;
        }

        // Re-index before appending to keep keys sequential
        $this->items   = array_values($this->items);
        $this->items[] = ['item_description' => '', 'unit' => '', 'quantity' => '', 'unit_price' => ''];

        // Jump to the last page so the new blank row is immediately visible
        $this->itemPage = $this->totalItemPages;
    }

    // -------------------------------------------------------------------------
    // Remove an item row by its index
    // Clamps the current page if the last page becomes empty after removal
    // -------------------------------------------------------------------------
    public function removeItem(int $index): void
    {
        array_splice($this->items, $index, 1);

        if ($this->itemPage > $this->totalItemPages) {
            $this->itemPage = $this->totalItemPages;
        }
    }

    // -------------------------------------------------------------------------
    // Parse pasted text into line items
    // Supports tab-separated (Excel) and comma-separated formats
    // Column order: Description, Unit, Quantity, Unit Price (optional)
    // -------------------------------------------------------------------------
    public function parsePastedItems(): void
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($this->pasteText));
        $lines = array_filter($lines, fn($line) => trim($line) !== '');
        $added = 0;

        foreach ($lines as $line) {
            // Auto-detect separator: tab (Excel) or comma
            $separator = str_contains($line, "\t") ? "\t" : ",";
            $cols      = array_map('trim', explode($separator, $line));

            // Skip rows that don't have at least description, unit, and quantity
            if (count($cols) < 3) continue;

            $this->items[] = [
                'item_description' => $cols[0] ?? '',
                'unit'             => $cols[1] ?? '',
                'quantity'         => $cols[2] ?? '',
                'unit_price'       => $cols[3] ?? '',
            ];
            $added++;
        }

        // Re-index to keep keys clean after appending
        $this->items = array_values($this->items);

        if ($added > 0) {
            // Close the paste area and jump to last page to show imported rows
            $this->pasteText     = '';
            $this->showPasteArea = false;
            $this->itemPage      = $this->totalItemPages;
        } else {
            $this->addError('pasteText', 'Could not parse any items. Each line needs at least: Description, Unit, Quantity.');
        }
    }

    // -------------------------------------------------------------------------
    // Computed: filtered items
    // Filters by description or unit based on the search term.
    // Blank rows are always shown so they can be filled in or removed.
    // Keys are preserved so wire:model binds to the correct $this->items index.
    // -------------------------------------------------------------------------
    public function getFilteredItemsProperty(): array
    {
        $filtered = [];
        foreach ($this->items as $i => $item) {
            $descriptionEmpty = trim($item['item_description'] ?? '') === '';
            if (
                empty($this->itemSearch) ||
                $descriptionEmpty ||
                str_contains(strtolower($item['item_description'] ?? ''), strtolower($this->itemSearch)) ||
                str_contains(strtolower($item['unit'] ?? ''), strtolower($this->itemSearch))
            ) {
                $filtered[$i] = $item;
            }
        }
        return $filtered;
    }

    // -------------------------------------------------------------------------
    // Computed: current page of filtered items
    // preserve_keys=true keeps the original $this->items index as the key
    // so wire:model="items.{{ $index }}.field" always targets the right row
    // -------------------------------------------------------------------------
    public function getPagedItemsProperty(): array
    {
        return array_slice(
            $this->filteredItems,
            ($this->itemPage - 1) * $this->itemsPerPage,
            $this->itemsPerPage,
            true // preserve keys
        );
    }

    // -------------------------------------------------------------------------
    // Computed: total number of pages based on filtered item count
    // Always at least 1 so the pagination never breaks on empty results
    // -------------------------------------------------------------------------
    public function getTotalItemPagesProperty(): int
    {
        return max(1, (int) ceil(count($this->filteredItems) / $this->itemsPerPage));
    }

    // Pagination controls
    public function itemNextPage(): void
    {
        if ($this->itemPage < $this->totalItemPages) $this->itemPage++;
    }

    public function itemPrevPage(): void
    {
        if ($this->itemPage > 1) $this->itemPage--;
    }

    // Reset to page 1 whenever the search term changes
    public function updatedItemSearch(): void
    {
        $this->itemPage = 1;
    }

    // -------------------------------------------------------------------------
    // Save — handles both create and update
    // -------------------------------------------------------------------------
    public function save(): void
    {
        // --- Status trappings ---

        // A Lost RFQ is locked — its status cannot be changed to anything else
        if ($this->rfqId) {
            $current = Rfq::findOrFail($this->rfqId);
            if ($current->status === 'Lost' && $this->status !== 'Lost') {
                $this->addError('status', 'A Lost RFQ cannot be changed to another status.');
                return;
            }
        }

        // Status can only be set to Quoted if at least one item has a unit price
        if ($this->status === 'Quoted') {
            $hasAnyPriced = collect($this->items)->some(fn($item) => !empty($item['unit_price']));
            if (!$hasAnyPriced) {
                $this->addError('status', 'Status can only be set to Quoted when at least one item has a unit price.');
                return;
            }
        }

        // --- Validation ---
        $this->validate([
            'agency_id'                => 'required|exists:agencies,id',
            'date_received'            => 'required|date',
            'deadline'                 => 'nullable|date|after_or_equal:date_received',
            'abc'                      => 'nullable|numeric|min:0',
            'status'                   => 'required|in:Received,Reviewing,Quoted,Awarded,Lost',
            'notes'                    => 'nullable|string',
            'philgeps_ref'             => 'nullable|string',
            'items.*.item_description' => 'required|string',
            'items.*.unit'             => 'required|string',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.unit_price'       => 'nullable|numeric|min:0',
        ]);

        // --- Prepare data ---
        $data = [
            'agency_id'     => $this->agency_id,
            'date_received' => $this->date_received,
            'deadline'      => $this->deadline ?: null,
            'abc'           => $this->abc ?: null,
            'status'        => $this->status,
            'notes'         => $this->notes ?: null,
            'philgeps_ref'  => $this->philgeps_ref ?: null,
        ];

        if ($this->rfqId) {
            // Update existing RFQ — delete old items and re-insert below
            $rfq = Rfq::findOrFail($this->rfqId);
            $rfq->update($data);
            $rfq->items()->delete();
        } else {
            // Create new RFQ — auto-generate number if left blank
            $data['rfq_number'] = $this->rfq_number ?: Rfq::generateNumber();
            $rfq = Rfq::create($data);
        }

        // --- Re-insert line items ---
        foreach ($this->items as $item) {
            $rfq->items()->create([
                'item_description' => $item['item_description'],
                'unit'             => $item['unit'],
                'quantity'         => $item['quantity'],
                'unit_price'       => $item['unit_price'] ?: null,
                // Auto-calculate total price if both fields are present
                'total_price'      => ($item['unit_price'] && $item['quantity'])
                                        ? $item['unit_price'] * $item['quantity']
                                        : null,
            ]);
        }

        // --- Auto-update status based on deadline and pricing ---
        $allPriced = collect($this->items)->every(fn($item) => !empty($item['unit_price']));

        // Only check overdue if a deadline is set
        $isOverdue = $this->deadline && now()->startOfDay()->gt(\Carbon\Carbon::parse($this->deadline)->startOfDay());

        if ($isOverdue) {
            // Deadline has passed — mark as Lost regardless of pricing
            $rfq->update(['status' => 'Lost']);
        } elseif (in_array($rfq->status, ['Received', 'Quoted'])) {
            // Auto-promote to Quoted if all items are priced, otherwise keep as Received
            $rfq->update(['status' => $allPriced ? 'Quoted' : 'Received']);
        }

        session()->flash('message', "RFQ {$rfq->rfq_number} saved successfully.");
        $this->redirect(route('rfqs.index'));
    }

    // -------------------------------------------------------------------------
    // Render — passes all necessary data to the blade view
    // -------------------------------------------------------------------------
    public function render()
    {
        return view('livewire.rfq-form', [
            'agencies'       => Agency::orderBy('name')->get(),
            'rfqId'          => $this->rfqId,
            'pagedItems'     => $this->pagedItems,
            'totalItemPages' => $this->totalItemPages,
            'filteredItems'  => $this->filteredItems,
            'totalItemCount' => count($this->items),
        ]);
    }
}