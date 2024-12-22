<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;

class AssignEngineerRequest extends BaseTicketRequest
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

        $engineerAttribute = 'data.attributes.engineer';

        return [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            $engineerAttribute => 'required|integer|exists:users,id|exists:engineers,user_id',
        ];

    }
}
