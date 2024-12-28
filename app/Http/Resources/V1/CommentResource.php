<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'comment',
            'id' => $this->id,
            'attributes' => [
                'ticket' => $this->ticket_id,
                'user' => $this->user_id,
                'comment' => $this->comment,
            ],
            'links' => ''
        ];
    }
}
