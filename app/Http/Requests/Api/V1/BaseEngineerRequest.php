<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseEngineerRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = array_merge([
            'data.attributes.user' => 'user_id'
        ], $otherAttributes);

        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }
}
