<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rfq extends Model
{
  protected $fillable = [
    'rfq_number',
    'agency_id',
    'date_received',
    'deadline',
    'abc',
    'status',
    'notes',
    'philgeps_ref',
    'attachment_path',
    'documents',
];

    protected $casts = [
        'date_received' => 'date',
        'deadline'      => 'date',
        'abc'           => 'decimal:2',
          'documents' => 'array',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RfqItem::class);
    }

   public function getDaysLeftAttribute(): int
{
    return (int) round(now()->startOfDay()->diffInDays($this->deadline->startOfDay(), false));
}

    public function getTotalQuotedAttribute(): float
    {
        return $this->items->sum('total_price');
    }
public static function generateNumber(): string
{
    $year = now()->year;
    $last = static::whereYear('created_at', $year)
        ->orderByRaw('CAST(SUBSTRING_INDEX(rfq_number, "-", -1) AS UNSIGNED) DESC')
        ->value('rfq_number');

    $next = $last ? (int) explode('-', $last)[2] + 1 : 1;

    return sprintf('RFQ-%d-%03d', $year, $next);
}
public function getDaysSinceReceivedAttribute(): int
{
    return (int) round($this->date_received->startOfDay()->diffInDays(now()->startOfDay()));
}
}