<?php

namespace App\Console\Commands;

use App\Models\Rfq;
use Illuminate\Console\Command;

class UpdateOverdueRfqs extends Command
{
    protected $signature   = 'rfqs:update-overdue';
    protected $description = 'Mark overdue RFQs as Lost';

    public function handle(): void
{
    // Mark overdue as Lost
    $lost = Rfq::whereNotIn('status', ['Awarded', 'Lost'])
        ->where('deadline', '<', now()->startOfDay())
        ->update(['status' => 'Lost']);

    // Mark as Reviewing if received 1+ days ago and still Received
    $reviewing = Rfq::where('status', 'Received')
        ->where('date_received', '<', now()->startOfDay())
        ->update(['status' => 'Reviewing']);

    $this->info("Marked {$lost} overdue RFQ(s) as Lost.");
    $this->info("Marked {$reviewing} RFQ(s) as Reviewing.");
}
}