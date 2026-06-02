<?php

namespace App\Http\Controllers;

use App\Models\Rfq;
use App\Models\Agency;
use Illuminate\Http\Request;

class RfqController extends Controller
{
    /**
     * Show the form for creating a new RFQ.
     * Loads all agencies sorted by name for the dropdown.
     */
    public function create()
    {
        $agencies = Agency::orderBy('name')->get();
        return view('rfqs.create', compact('agencies'));
    }

    /**
     * Store a newly created RFQ in the database.
     * Auto-generates an RFQ number if the user left it blank.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rfq_number'    => 'nullable|string|unique:rfqs,rfq_number',
            'agency_id'     => 'required|exists:agencies,id',
            'date_received' => 'required|date',
            'deadline'      => 'required|date|after_or_equal:date_received',
            'abc'           => 'nullable|numeric|min:0',
            'status'        => 'required|in:Received,Reviewing,Quoted,Awarded,Lost',
            'notes'         => 'nullable|string',
            'philgeps_ref'  => 'nullable|string',
        ]);

        // Auto-generate RFQ number if not provided
        if (empty($validated['rfq_number'])) {
            $validated['rfq_number'] = Rfq::generateNumber();
        }

        $rfq = Rfq::create($validated);

        return redirect()->route('rfqs.index')
                         ->with('message', "RFQ {$rfq->rfq_number} created successfully.");
    }

    /**
     * Display the details of a specific RFQ.
     * Automatically transitions status from Received to Reviewing
     * on first view, indicating the RFQ is being evaluated.
     */
    public function show(Rfq $rfq)
    {
        if ($rfq->status === 'Received') {
            $rfq->update(['status' => 'Reviewing']);
        }

        return view('rfqs.show', compact('rfq'));
    }

    /**
     * Show the form for editing an existing RFQ.
     * The RFQ is passed via route model binding.
     */
    public function edit(Rfq $rfq)
    {
        return view('rfqs.edit', compact('rfq'));
    }

    /**
     * Update an existing RFQ in the database.
     * RFQ number is excluded from updates to prevent changing it after creation.
     */
    public function update(Request $request, Rfq $rfq)
    {
        $validated = $request->validate([
            'agency_id'     => 'required|exists:agencies,id',
            'date_received' => 'required|date',
            'deadline'      => 'required|date|after_or_equal:date_received',
            'abc'           => 'nullable|numeric|min:0',
            'status'        => 'required|in:Received,Reviewing,Quoted,Awarded,Lost',
            'notes'         => 'nullable|string',
            'philgeps_ref'  => 'nullable|string',
        ]);

        $rfq->update($validated);

        return redirect()->route('rfqs.show', $rfq)
                         ->with('message', "RFQ {$rfq->rfq_number} updated successfully.");
    }

    /**
     * Delete an RFQ and its associated items from the database.
     * Redirects back to the RFQ tracker list after deletion.
     */
    public function destroy(Rfq $rfq)
    {
        $rfq->delete();
        return redirect()->route('rfqs.index')
                         ->with('message', 'RFQ deleted successfully.');
    }
    public function print(Rfq $rfq)
{
    $rfq->load('agency', 'items');
    return view('rfqs.print', compact('rfq'));
}
}