<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    
    public $table = 'invoices';
    protected $guarded = ['id'];
    protected $appends = ['total'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invoiceitems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function getTotalAttribute()
    {
        return $this->invoiceitems->sum->total;
    }
}
