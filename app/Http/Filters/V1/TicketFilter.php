<?php

namespace App\Http\Filters\V1;

use App\Models\Ticket;

class TicketFilter extends QueryFilter
{
    protected $sortable = [
        'title',
        'status',
        'priority',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    public function createdAt($value)
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('created_at', $dates);
        }

        return $this->builder->whereDate('created_at', $value);
    }

    public function include($value)
    {
        return $this->builder->with(explode(',', $value));
    }

    public function status($value)
    {
        return $this->builder->whereIn('status', explode(',', $value));
    }

    public function priority($value)
    {
        $priorityMap = Ticket::priorityMap;
        $priorityValue = $priorityMap[strtolower($value)] ?? null;

        if ($priorityValue !== null) {
            return $this->builder->where('priority', $priorityValue);
        }

        return $this->builder;
    }

    public function title($value)
    {
        $likeStr = str_replace('*', '%', $value);
        return $this->builder->where('title', 'like', $likeStr);
    }

    public function updatedAt($value)
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->whereDate('updated_at', $value);
    }

    public function assigned($value)
    {
        $hasEngineer = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        return $this->builder->hasEngineer($hasEngineer);
    }
}
