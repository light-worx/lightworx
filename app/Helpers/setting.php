<?php

use App\Models\Setting; 

if (! function_exists('setting')) {
    function setting(string $slug, $default = null)
    {
        return Setting::where('setting', $slug)->value('value') ?? $default;
    }
}