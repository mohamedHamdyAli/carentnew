<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name_en' => 'sometimes|string',
            'name_ar' => 'sometimes|string',
            'brand_id' => 'sometimes|exists:brands,id',
            'display_order' => 'integer',
            'active' => 'boolean',
        ];
    }
}
