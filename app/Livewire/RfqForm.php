<?php

namespace App\Livewire;

use App\Models\Rfq;
use App\Models\Agency;
use App\Models\RfqItem;
use Livewire\Component;

class RfqForm extends Component
{
    // RFQ fields
    public string $rfq_number    = '';
    public string $agency_id     = '';
    public string $date_received = '';
    public string $deadline      = '';
    public string $abc           = '';
    public string $status        = 'Received';
    public string $notes         = '';
    public string $philgeps_ref  = '';
    public array  $items         = [
        ['item_description' => '', 'unit' => '', 'quantity' => '', 'unit_price' => ''],
    ];
    public ?int $rfqId = null;

    // Search & pagination
    public string $itemSearch   = '';
    public int    $itemsPerPage = 5;
    public int    $itemPage     = 1;

    public function mount(?int $rfqId = null): void
    {
        if ($rfqId) {
            $rfq = Rfq::with('items')->findOrFail($rfqId);
            $this->rfqId         = $rfq->id;
            $this->rfq_number    = $rfq->rfq_number;
            $this->agency_id     = (string) $rfq->agency_id;
            $this->date_received = $rfq->date_received->format('Y-m-d');
            $this->deadline      = $rfq->deadline->format('Y-m-d');
            $this->abc           = (string) ($rfq->abc ?? '');
            $this->status        = $rfq->status;
            $this->notes         = $rfq->notes ?? '';
            $this->philgeps_ref  = $rfq->philgeps_ref ?? '';
            // array_values() ensures clean 0-based integer keys from the start
            $this->items = array_values($rfq->items->map(fn($i) => [
                'item_description' => $i->item_description,
                'unit'             => $i->unit,
                'quantity'         => (string) $i->quantity,
                'unit_price'       => (string) ($i->unit_price ?? ''),
            ])->toArray());
        } else {
            $this->date_received = now()->format('Y-m-d');
        }
    }

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

    $this->items   = array_values($this->items);
    $this->items[] = ['item_description' => '', 'unit' => '', 'quantity' => '', 'unit_price' => ''];
    $this->itemPage = $this->totalItemPages;
}

public function removeItem(int $index): void
{
    array_splice($this->items, $index, 1);
    if ($this->itemPage > $this->totalItemPages) {
        $this->itemPage = $this->totalItemPages;
    }
}

    // --- Computed properties (preserve real keys so wire:model binds correctly) ---

public function getFilteredItemsProperty(): array
{
    // Returns [visualIndex => item] always 0-based, no key tricks
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

 public function getPagedItemsProperty(): array
{
    return array_slice($this->filteredItems, ($this->itemPage - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
}

    public function getTotalItemPagesProperty(): int
    {
        return max(1, (int) ceil(count($this->filteredItems) / $this->itemsPerPage));
    }

    public function itemNextPage(): void
    {
        if ($this->itemPage < $this->totalItemPages) $this->itemPage++;
    }

    public function itemPrevPage(): void
    {
        if ($this->itemPage > 1) $this->itemPage--;
    }

    public function updatedItemSearch(): void
    {
        $this->itemPage = 1;
    }

    // --- Save ---

    public function save(): void
    {
        $this->validate([
            'agency_id'                => 'required|exists:agencies,id',
            'date_received'            => 'required|date',
            'deadline'                 => 'required|date|after_or_equal:date_received',
            'abc'                      => 'nullable|numeric|min:0',
            'status'                   => 'required|in:Received,Reviewing,Quoted,Awarded,Lost',
            'notes'                    => 'nullable|string',
            'philgeps_ref'             => 'nullable|string',
            'items.*.item_description' => 'required|string',
            'items.*.unit'             => 'required|string',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.unit_price'       => 'nullable|numeric|min:0',
        ]);

        $data = [
            'agency_id'     => $this->agency_id,
            'date_received' => $this->date_received,
            'deadline'      => $this->deadline,
            'abc'           => $this->abc ?: null,
            'status'        => $this->status,
            'notes'         => $this->notes ?: null,
            'philgeps_ref'  => $this->philgeps_ref ?: null,
        ];

        if ($this->rfqId) {
            $rfq = Rfq::findOrFail($this->rfqId);
            $rfq->update($data);
            $rfq->items()->delete();
        } else {
            $data['rfq_number'] = $this->rfq_number ?: Rfq::generateNumber();
            $rfq = Rfq::create($data);
        }

        foreach ($this->items as $item) {
            $rfq->items()->create([
                'item_description' => $item['item_description'],
                'unit'             => $item['unit'],
                'quantity'         => $item['quantity'],
                'unit_price'       => $item['unit_price'] ?: null,
                'total_price'      => ($item['unit_price'] && $item['quantity'])
                                        ? $item['unit_price'] * $item['quantity']
                                        : null,
            ]);
        }
        $allPriced = collect($this->items)->every(fn($item) => !empty($item['unit_price']));
        $rfq->update(['status' => $allPriced ? 'Quoted' : 'Received']);

        session()->flash('message', "RFQ {$rfq->rfq_number} saved successfully.");
        $this->redirect(route('rfqs.show', $rfq));
        session()->flash('message', "RFQ {$rfq->rfq_number} saved successfully.");
        $this->redirect(route('rfqs.show', $rfq));
    }

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