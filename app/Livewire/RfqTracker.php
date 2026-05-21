<?php

namespace App\Livewire;

use App\Models\Rfq;
use Livewire\Component;
use Livewire\WithPagination;

class RfqTracker extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $status  = 'all';
    public string $sortBy  = 'deadline';
    public string $sortDir = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
    }

    public function updateStatus(int $rfqId, string $status): void
    {
        $rfq = Rfq::findOrFail($rfqId);
        $rfq->update(['status' => $status]);
        session()->flash('message', "RFQ #{$rfq->rfq_number} updated to {$status}.");
    }

    public function getMetricsProperty(): array
    {
        $all     = Rfq::count();
        $pending = Rfq::whereIn('status', ['Received', 'Reviewing'])->count();
        $quoted  = Rfq::whereIn('status', ['Quoted', 'Awarded', 'Lost'])->count();
        $awarded = Rfq::where('status', 'Awarded')->count();

        return [
            'total'    => $all,
            'pending'  => $pending,
            'quoted'   => $quoted,
            'win_rate' => $quoted > 0 ? round(($awarded / $quoted) * 100) : 0,
        ];
    }

    public function render()
    {
        $rfqs = Rfq::with('agency', 'items')
            ->when($this->search, function ($q) {
                $q->where('rfq_number', 'like', "%{$this->search}%")
                  ->orWhereHas('agency', fn($a) =>
                      $a->where('name', 'like', "%{$this->search}%")
                  );
            })
            ->when($this->status !== 'all', fn($q) =>
                $q->where('status', $this->status)
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(15);

return view('livewire.rfq-tracker', [
    'rfqs'    => $rfqs,
    'metrics' => $this->metrics,
]);
    }
}