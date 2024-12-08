<?php

namespace App\Http\Requests\Api\V1;

class ReplaceUserRequest extends BaseTicketRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.attributes.name' => 'required|string',
            'data.attributes.email' => 'sometimes|email|string|unique:users,email,',
            'data.attributes.isAdmin' => 'required|boolean',
            'data.attributes.password' => 'required|string',
        ];
    }
}
