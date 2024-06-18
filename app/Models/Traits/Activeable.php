<?php

namespace App\Models\Traits;

trait Activeable
{
    /**
     * Scope a query to only include active sessions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('is_active', 1);
    }

    /**
     * Scope a query to only include in_active sessions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeInActive($query)
    {
        $query->where('is_active', 0);
    }
}
