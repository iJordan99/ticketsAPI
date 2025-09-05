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
            'data.attributes.status' => 'required|string|size:1|in:A,C,H,X,N',
            'data.attributes.type' => 'required|string|in:incident,problem,question,request',
            'data.attributes.priority' => $isTicketsController ? 'nullable|string|in:low,medium,high' : 'required|string|in:low,medium,high',
            'data.attributes.reproduction_step' => 'sometimes|string',
            'data.attributes.error_code' => 'sometimes|string',
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
                'description' => "The ticket's title",
                'example' => 'Unable to Access Email on Outlook'
            ],
            'data.attributes.description' => [
                'description' => "The ticket's description",
                'example' => 'Unable to access my company email account using Outlook on my work laptop i receive the following error message "Cannot connect to the server."',
            ],
            'data.attributes.status' => [
                'description' => "The ticket's status (A: Active, C: Closed, H: On Hold, X: Cancelled, N: New)",
                'example' => 'N',
            ],
            'data.attributes.type' => [
                'description' => "The ticket type",
                'example' => 'incident'
            ],
            'data.attributes.priority' => [
                'description' => "The ticket priority",
                'example' => 'medium'
            ],
            'data.attributes.reproductionStep' => [
                'description' => "Steps to reproduce the issue",
                'example' => '1. Open Outlook 2. Click on Send/Receive 3. Error appears'
            ],
            'data.attributes.errorCode' => [
                'description' => "The error code associated with the ticket",
                'example' => 'ERR_CONNECTION_TIMEOUT'
            ],
        ];

        if ($this->routeIs('tickets.store')) {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => '1'
            ];
        } else {
            $documentation['author'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => '1'
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
