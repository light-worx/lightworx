<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Client extends Model
{
    
    public $table = 'clients';
    protected $guarded = ['id'];

    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(Invoice::class, Project::class);
    }

    public function sentinvoices(): HasManyThrough
    {
        return $this->hasManyThrough(Invoice::class, Project::class)->where('invoicedate', '!=', null);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getContactAttribute(){
        return $this->contact_firstname . " " . $this->contact_surname;
    }

    public function getBalanceAttribute()
    {
        $totalInvoiced = $this->sentinvoices->sum->total;
        $totalPaid = $this->payments->sum('amount');
        return $totalInvoiced - $totalPaid;
    }

}
