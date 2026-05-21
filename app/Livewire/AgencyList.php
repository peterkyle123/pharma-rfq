<?php

namespace App\Livewire;

use App\Models\Agency;
use Livewire\Component;
use Livewire\WithPagination;

class AgencyList extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

public function delete(int $id): void
{
    $agency = Agency::findOrFail($id);
    $agency->delete();
    session()->flash('message', "Agency {$agency->name} deleted.");
    $this->resetPage();
}

    public function render()
    {
        $agencies = Agency::withCount('rfqs')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('type', 'like', "%{$this->search}%")
            )
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.agency-list', compact('agencies'));
    }
}