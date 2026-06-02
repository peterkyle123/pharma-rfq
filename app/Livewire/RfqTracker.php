<?php

namespace App\Livewire;

use App\Models\Rfq;
use Livewire\Component;
use Livewire\WithPagination;

class RfqTracker extends Component
{
    use WithPagination;

    // -------------------------------------------------------------------------
    // Filter, sort, and UI state
    // -------------------------------------------------------------------------

    // Search term for filtering by RFQ number or agency name
    public string $search  = '';

    // Active status filter tab — 'all' shows everything
    public string $status  = 'all';

    // Column currently being sorted and its direction
    public string $sortBy  = 'deadline';
    public string $sortDir = 'asc';

    // Tracks which RFQ rows have their document checklist dropdown open
    public array $openRows = [];

    // Persist search and status filters in the URL query string
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'all'],
    ];

    // Reset to page 1 whenever search or status filter changes
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    // -------------------------------------------------------------------------
    // Filter by status tab
    // -------------------------------------------------------------------------
    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    // -------------------------------------------------------------------------
    // Column sorting
    // Toggles direction if the same column is clicked again,
    // otherwise switches to the new column with ascending order
    // -------------------------------------------------------------------------
    public function sortColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
    }

    // -------------------------------------------------------------------------
    // Toggle the document checklist dropdown for a specific RFQ row
    // Adds to openRows to open, removes to close
    // -------------------------------------------------------------------------
    public function toggleOpen(int $rfqId): void
    {
        if (in_array($rfqId, $this->openRows)) {
            $this->openRows = array_values(
                array_filter($this->openRows, fn($id) => $id !== $rfqId)
            );
        } else {
            $this->openRows[] = $rfqId;
        }
    }

    // -------------------------------------------------------------------------
    // Manually update the status of an RFQ from the tracker
    // -------------------------------------------------------------------------
    public function updateStatus(int $rfqId, string $status): void
    {
        $rfq = Rfq::findOrFail($rfqId);
        $rfq->update(['status' => $status]);
        session()->flash('message', "RFQ #{$rfq->rfq_number} updated to {$status}.");
    }

    // -------------------------------------------------------------------------
    // Toggle a document checkbox on/off for a given RFQ
    // When Notice of Award (NOA) is checked, status auto-updates to Awarded.
    // When NOA is unchecked, status reverts to Quoted.
    // -------------------------------------------------------------------------
public function toggleDoc(int $rfqId, string $doc): void
{
    $rfq     = Rfq::findOrFail($rfqId);
    $docs    = $rfq->documents ?? [];
    $current = $docs[$doc] ?? false;

    // Prevent checking NOA or NTP if the RFQ is Lost
if (in_array($doc, ['notice_of_award', 'purchase_order', 'ntp']) && $rfq->status === 'Lost') {
    $this->addError('doc_error_' . $rfqId, 'Cannot mark this document on a Lost RFQ.');
    return;
}

    $docs[$doc] = $current ? false : ['received' => true, 'date' => null];
    $rfq->update(['documents' => $docs]);

    // Any of these three documents indicates the RFQ was awarded
    if (in_array($doc, ['notice_of_award', 'purchase_order', 'ntp'])) {
        $rfq->update([
            'status' => $current ? 'Quoted' : 'Awarded',
        ]);
    }
}

    // -------------------------------------------------------------------------
    // Save the date received for a specific document
    // Called when the user picks a date on a checked document
    // -------------------------------------------------------------------------
    public function setDocDate(int $rfqId, string $doc, string $date): void
    {
        $rfq  = Rfq::findOrFail($rfqId);
        $docs = $rfq->documents ?? [];

        // Keep received flag true and update only the date
        $docs[$doc] = ['received' => true, 'date' => $date];
        $rfq->update(['documents' => $docs]);
    }

    // -------------------------------------------------------------------------
    // Computed metrics for the dashboard cards at the top of the tracker
    // -------------------------------------------------------------------------
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
        'awarded'  => $awarded,
        'win_rate' => $quoted > 0 ? round(($awarded / $quoted) * 100) : 0,
    ];
}
    // -------------------------------------------------------------------------
    // Render — builds the paginated, filtered, sorted RFQ list
    // -------------------------------------------------------------------------
    public function render()
{
    $rfqs = Rfq::with('agency', 'items')

        // Filter by search term — matches RFQ number or agency name
        ->when($this->search, function ($q) {
            $q->where('rfq_number', 'like', "%{$this->search}%")
              ->orWhereHas('agency', fn($a) =>
                  $a->where('name', 'like', "%{$this->search}%")
              );
        })

        // Filter by status tab if not showing all
        ->when($this->status !== 'all', fn($q) =>
            $q->where('status', $this->status)
        )

        // Sorting — agency requires a join, total_quoted requires a subquery sum,
        // all other columns can be sorted directly on the rfqs table
        ->when($this->sortBy === 'agency_id', function ($q) {
            $q->join('agencies', 'rfqs.agency_id', '=', 'agencies.id')
              ->orderBy('agencies.name', $this->sortDir)
              ->select('rfqs.*');
        })
        ->when($this->sortBy === 'total_quoted', function ($q) {
            $q->withSum('items', 'total_price')
              ->orderBy('items_sum_total_price', $this->sortDir);
        })
        ->when(!in_array($this->sortBy, ['agency_id', 'total_quoted']), function ($q) {
            $q->orderBy($this->sortBy, $this->sortDir);
        })

        ->paginate(15);

    return view('livewire.rfq-tracker', [
        'rfqs'    => $rfqs,
        'metrics' => $this->metrics,
    ]);
}
}
