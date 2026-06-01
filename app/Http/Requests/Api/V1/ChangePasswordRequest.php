<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string'
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
        ];
    }
}