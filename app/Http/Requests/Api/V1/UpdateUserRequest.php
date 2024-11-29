<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateUserRequest extends BaseUserRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data.attributes.name' => 'sometimes|string',
            'data.attributes.email' => 'sometimes|email|string|unique:users,email,' . $this->route('user'),
            'data.attributes.isAdmin' => 'sometimes|boolean',
            'data.attributes.password' => 'sometimes|string',
        ];
    }
}
