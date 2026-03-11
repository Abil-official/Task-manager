<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TaskFilters
{
    protected $request;

    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        // We loop through the request and call methods if they exist
        foreach ($this->request->all() as $filter => $value) {
            if (method_exists($this, $filter) && ! empty($value)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    // Individual Filter Logic
    protected function location($id)
    {
        return $this->builder->where('location_id', $id);
    }

    protected function min_price($price)
    {
        return $this->builder->where('price', '>=', $price);
    }

    protected function sort($column)
    {
        // Example: ?sort=price_desc
        $direction = str_contains($column, '_desc') ? 'desc' : 'asc';
        $field = str_replace(['_asc', '_desc'], '', $column);

        return $this->builder->orderBy($field, $direction);
    }
}
