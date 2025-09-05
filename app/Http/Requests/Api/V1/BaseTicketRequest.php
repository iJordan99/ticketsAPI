<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = [])
    {
        $attributeMap = array_merge([
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.type' => 'type',
            'data.attributes.status' => 'status',
            'data.attributes.priority' => strtolower('priority'),
            'data.attributes.reproduction_step' => 'reproduction_step',
            'data.attributes.error_code' => 'error_code',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'user_id',
            'data.relationships.engineer.data.id' => 'engineer_id',
        ], $otherAttributes);

        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        if ($this->has('data.relationships.engineer.data')) {
            $attributesToUpdate['engineer_ids'] = collect($this->input('data.relationships.engineer.data'))
                ->pluck('id')
                ->toArray();
        }

        return $attributesToUpdate;
    }

    public function messages()
    {
        return [
            'data.attributes.status' => 'The data.attributes.status value is invalid. Please use A, C, H,X, or N'
        ];
    }
}
