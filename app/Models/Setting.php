<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    
    public $table = 'settings';
    protected $guarded = ['id'];

    public static function getValue(string $slug, $default = null)
    {
        return static::where('setting', $slug)->value('value') ?? $default;
    }

}
