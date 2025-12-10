<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Hour extends Model
{
    
    public $table = 'hours';
    protected $guarded = ['id'];
    public $timestamps = false;
    
    public function hourable(): MorphTo
    {
        return $this->morphTo();
    }
}
