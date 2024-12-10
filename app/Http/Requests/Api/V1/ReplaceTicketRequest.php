<?php

namespace App\Http\Requests\Api\V1;

class ReplaceTicketRequest extends BaseTicketRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            'data.attributes.priority' => 'integer|min:1|max:3',
            'data.relationships' => 'required|array',
            'data.relationships.author' => 'required|array',
            'data.relationships.author.data' => 'required|array',
            'data.relationships.author.data.id' => 'required|integer',
        ];
    }
}
