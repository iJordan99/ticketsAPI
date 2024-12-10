<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $isTicketsController = $this->routeIs('tickets.store');
        $authorIdAttr = $isTicketsController ? 'data.relationships.author.data.id' : 'author';
        $user = Auth::user();
        $authorRule = 'required|integer|exists:users,id';

        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            'data.attributes.priority' => 'string|in:low,medium,high,Low,Medium,High',
        ];

        if ($isTicketsController) {
            $rules['data.relationships'] = 'required|array';
            $rules['data.relationships.author'] = 'required|array';
            $rules['data.relationships.author.data'] = 'required|array';
        }

        $rules[$authorIdAttr] = $authorRule . '|size:' . $user->id;

        if ($user->tokenCan(Abilities::CreateAuthorTicket)) {
            $rules[$authorIdAttr] = $authorRule;
        }

        return $rules;
    }

    public function bodyParameters(): array
    {
        $documentation = [
            'data.attributes.title' => [
                'description' => "The ticket's title (method)",
                'example' => 'No-example'
            ],
            'data.attributes.description' => [
                'description' => "The ticket's description",
                'example' => 'No-example',
            ],
            'data.attributes.status' => [
                'description' => "The ticket's status",
                'example' => 'No-example',
            ],
        ];

        if ($this->routeIs('tickets.store')) {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => 'No-example'
            ];
        } else {
            $documentation['author'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => 'No-example'
            ];
        }

        return $documentation;

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
