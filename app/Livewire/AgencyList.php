<?php

namespace App\Livewire;

use App\Models\Agency;
use Livewire\Component;
use Livewire\WithPagination;

class AgencyList extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $sortBy  = 'name';
    public string $sortDir = 'asc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sortColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
    }

    public function delete(int $id): void
    {
        $agency = Agency::withCount('rfqs')->findOrFail($id);

        if ($agency->rfqs_count > 0) {
            session()->flash('error', "Cannot delete {$agency->name} — it has {$agency->rfqs_count} RFQ(s) attached.");
            return;
        }

        $agency->delete();
        session()->flash('message', "Agency {$agency->name} deleted.");
        $this->resetPage();
    }

public function render()
{
    $agencies = Agency::withCount([
        'rfqs',
        'rfqs as received_count'  => fn($q) => $q->where('status', 'Received'),
        'rfqs as reviewing_count' => fn($q) => $q->where('status', 'Reviewing'),
        'rfqs as quoted_count'    => fn($q) => $q->where('status', 'Quoted'),
        'rfqs as awarded_count'   => fn($q) => $q->where('status', 'Awarded'),
        'rfqs as lost_count'      => fn($q) => $q->where('status', 'Lost'),
    ])
        ->when($this->search, fn($q) =>
            $q->where('name', 'like', "%{$this->search}%")
              ->orWhere('type', 'like', "%{$this->search}%")
        )
        ->orderBy($this->sortBy, $this->sortDir)
        ->paginate(15);

    return view('livewire.agency-list', compact('agencies'));
}
}