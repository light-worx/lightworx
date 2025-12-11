<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    
    public $table = 'invoice_items';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $appends = ['total'];
    
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function getTotalAttribute()
    {
        return ($this->quantity ?? 0) * ($this->unit_price ?? 0);
    }
}
