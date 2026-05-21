<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqItem extends Model
{
    protected $fillable = [
        'rfq_id',
        'item_description',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    protected static function booted(): void
    {
        static::saving(function (RfqItem $item) {
            if ($item->unit_price && $item->quantity) {
                $item->total_price = $item->unit_price * $item->quantity;
            }
        });
    }
}