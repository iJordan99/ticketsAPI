<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Contracts\Validation\ValidationRule;

class  StoreTicketRequest extends BaseTicketRequest
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
        $authorIdAttr = $this->routeIs('tickets.store') ? 'data.relationships.author.data.id' : 'author';
        $user = $this->user();

        $authorRule = 'required|integer|exists:users,id';
        
        $rules = [
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
        ];

        if (!$this->routeIs('authors.tickets.store')) {
            $rules[$authorIdAttr] = $authorRule . '|size:' . $user->id;
        }

        if ($user->tokenCan(Abilities::CreateAuthorTicket)) {
            $rules[$authorIdAttr] = $authorRule;
        }

        return $rules;
    }


    protected function prepareForValidation(): void
    {
        if ($this->routeIs('authors.tickets.store')) {
            $this->merge([
                'author' => $this->route('author')->id, // Extract the ID
            ]);
        }
    }
}
