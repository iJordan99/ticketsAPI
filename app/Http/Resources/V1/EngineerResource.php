<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EngineerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'engineer',
            'id' => $this->id,
            'attributes' => [
                'ticket' => $this->pivot->ticket_id,
                'engineer' => $this->pivot->user_id,
                'assigned_at' => $this->pivot->created_at
            ],
            'links' => [
                'self' => route('engineer.tickets'),
            ]
        ];
    }
}
