<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasRecentGroupScope
{
    /**
     * Get the latest entry for each group.
     *
     * Each group is composed of one or more columns that make a unique combination to return the
     * last entry for.
     *
     * @param   \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $fields
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecentEachGroup(Builder $query, array $fields) : Builder
    {
        return $query->whereIn(static::getKeyName(), function ($query) use ($fields) {
            return $query->from(static::getTable())
                ->selectRaw('max(`' . static::getKeyName() . '`)')
                ->groupBy($fields);
        });
    }
}
