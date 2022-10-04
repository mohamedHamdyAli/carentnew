<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
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
            'country' => ['sometimes', 'nullable', 'string', 'exists:countries,country_code'],
            'target' => ['required', 'string', 'in:all,user,renters,owners,agencies'],
            'email' => ['sometimes', 'nullable', 'required_if:target,user', 'string', 'email', 'exists:users,email'],
            'language' => ['sometimes', 'nullable', 'string', 'in:en,ar'],
            'title' => ['required', 'string'],
            'message' => ['required', 'string'],
            'image' => ['sometimes', 'nullable', 'image', 'max:20480', 'mimes:jpg,jpeg,png'],
        ];
    }
}
