<?php

namespace App\Http\Filters\V1;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $builder;
    protected $request;
    protected $sortable = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }
    }

    protected function filter($arr)
    {
        foreach ($arr as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    protected function sort($value)
    {
        $sortAttr = explode(',', $value);

        foreach ($sortAttr as $sort) {
            $direction = 'asc';

            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $sort = substr($sort, 1);
            }

            if (!in_array($sort, $this->sortable) && !array_key_exists($sort, $this->sortable)) {
                continue;
            }

            $columnName = $this->sortable[$sort] ?? null;

            if ($columnName === null) {
                $columnName = $sort;
            }

            $this->builder->orderBy($columnName, $direction);
        }
    }
}
