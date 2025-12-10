<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Disbursement extends Model
{
    
    public $table = 'disbursements';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function disbursable(): MorphTo
    {
        return $this->morphTo();
    }

}
