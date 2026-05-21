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
public ?int $rfqId = null; // null = create, set = edit

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
        $this->items         = $rfq->items->map(fn($i) => [
            'item_description' => $i->item_description,
            'unit'             => $i->unit,
            'quantity'         => (string) $i->quantity,
            'unit_price'       => (string) ($i->unit_price ?? ''),
        ])->toArray();
    } else {
        $this->date_received = now()->format('Y-m-d');
    }
}

    public function addItem(): void
    {
        $this->items[] = ['item_description' => '', 'unit' => '', 'quantity' => '', 'unit_price' => ''];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

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
        // UPDATE existing
        $rfq = Rfq::findOrFail($this->rfqId);
        $rfq->update($data);
        $rfq->items()->delete();
    } else {
        // CREATE new
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

    session()->flash('message', "RFQ {$rfq->rfq_number} saved successfully.");
    $this->redirect(route('rfqs.show', $rfq));
}

 public function render()
{
    return view('livewire.rfq-form', [
        'agencies' => Agency::orderBy('name')->get(),
        'rfqId'    => $this->rfqId,
    ]);
}
}