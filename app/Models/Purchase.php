<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = [
        'grocery_id',
    ];

    public function grocery(): BelongsTo
    {
        return $this->belongsTo(Grocery::class);
    }
}
