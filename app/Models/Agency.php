<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    protected $fillable = [
        'name',
        'type',
        'region',
        'contact_person',
        'contact_email',
        'contact_phone',
    ];

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class);
    }

    public function getWinRateAttribute(): int
    {
        $quoted  = $this->rfqs()->whereIn('status', ['Quoted', 'Awarded', 'Lost'])->count();
        $awarded = $this->rfqs()->where('status', 'Awarded')->count();
        return $quoted > 0 ? (int) round(($awarded / $quoted) * 100) : 0;
    }
}