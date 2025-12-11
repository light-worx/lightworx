<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{

    public $table = 'projects';
    protected $guarded = ['id'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getBalanceAttribute()
    {
        return $this->invoices->sum->total;
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
