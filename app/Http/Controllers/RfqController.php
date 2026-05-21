<?php

namespace App\Http\Controllers;

use App\Models\Rfq;
use App\Models\Agency;
use Illuminate\Http\Request;

class RfqController extends Controller
{
    public function create()
    {
        $agencies = Agency::orderBy('name')->get();
        return view('rfqs.create', compact('agencies'));
    }

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

        return redirect()->route('rfqs.show', $rfq)
                         ->with('message', "RFQ {$rfq->rfq_number} created successfully.");
    }

    public function show(Rfq $rfq)
    {
        $rfq->load('agency', 'items');
        return view('rfqs.show', compact('rfq'));
    }

 public function edit(Rfq $rfq)
{
    return view('rfqs.edit', compact('rfq'));
}

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

    public function destroy(Rfq $rfq)
    {
        $rfq->delete();
        return redirect()->route('rfqs.index')
                         ->with('message', 'RFQ deleted successfully.');
    }
}