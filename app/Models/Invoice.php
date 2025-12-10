<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Invoice extends Model
{
    
    public $table = 'invoices';
    protected $guarded = ['id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function hours(): MorphMany
    {
        return $this->morphMany(Hour::class, 'hourable');
    }

    public function disbursements(): MorphMany
    {
        return $this->morphMany(Disbursement::class, 'disbursable');
    }
}
