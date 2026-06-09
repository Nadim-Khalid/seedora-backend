<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'exists:products,id'
            ],

            'rating' => [
                'required',
                'integer',
                'between:1,5'
            ],

            'review' => [
                'required',
                'string',
                'max:1000'
            ]
        ];
    }
}