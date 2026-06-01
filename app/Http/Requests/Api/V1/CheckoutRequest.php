<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
    return [
        'customer_name'     => 'required|string|max:255',
        'customer_phone'    => 'required|string|max:20',
        'shipping_address'  => 'required|string',
        'payment_method'    => 'required|in:cod,online',
    ];
}
}
