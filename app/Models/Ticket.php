<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{
    use HasFactory;

    const priorityMap = [
        'low' => 1,
        'medium' => 2,
        'high' => 3,
    ];
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'status',
        'reproduction_step',
        'error_code'
    ];
    protected $casts = [
        'status' => StatusEnum::class
    ];

    public function setPriorityAttribute($value): void
    {
        $this->attributes['priority'] = self::priorityMap[strtolower($value)] ?? null;
    }

    public function getPriorityAttribute($value): int|string|null
    {
        $reverseMap = array_flip(self::priorityMap);
        return $reverseMap[$value] ?? null;
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }
}
