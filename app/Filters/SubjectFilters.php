<?php

namespace App\Filters;

use App\User;

class SubjectFilters extends Filters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = ['name'];

    /**
     * Filter the query by a given name.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function name($query)
    {
        return $this->builder->where('name','like', "$query%");
    }

}