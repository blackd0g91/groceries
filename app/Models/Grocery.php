<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grocery extends Model {

    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'selected',
    ];

    protected $casts = [
        'amount' => 'integer',
        'selected' => 'boolean',
    ];

    public function purchases(): HasMany {
        return $this->hasMany(Purchase::class);
    }

    public function tausteSearchUrl(): string {
        return config('market.tauste.search_link') . urlencode($this->name);
    }

}
