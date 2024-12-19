<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'type' => 'ticket',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'description' => $this->when(
                    !$request->routeIs(['tickets.index', 'authors.tickets.index']),
                    $this->description
                ),
                'status' => $this->status,
                'priority' => $this->priority,
                'reproduction_step' => $this->reproduction_step,
                'error_code' => $this->error_code,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
            ],
            'includes' => [
                'author' => new UserResource($this->whenLoaded('author')),
                'engineer' => EngineerResource::collection($this->whenLoaded('engineer')),
            ],
            'links' => [
                'self' => route('tickets.show', ['ticket' => $this->id])
            ]
        ];
    }
}
