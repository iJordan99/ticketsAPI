<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Http\Filters\V1\QueryFilter;
use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([ModelObserver::class])]
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

    public function hasEngineer(): bool
    {
        return $this->engineer()->exists();
    }

    public function engineer(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assigned_tickets', 'ticket_id', 'user_id')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function scopeHasEngineer(Builder $query, $hasEngineer = true)
    {
        if ($hasEngineer) {
            return $query->whereHas('engineer');
        } else {
            return $query->whereDoesntHave('engineer');
        }
    }
}
