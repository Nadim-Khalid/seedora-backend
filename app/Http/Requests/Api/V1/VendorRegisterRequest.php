<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VendorRegisterRequest extends FormRequest
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
            'name'=> 'required|string|min:5|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:8|confirmed',
            'store_name'=>'required|string|min:3|max:255',
            'phone'=>'required|string|min:10|max:15',
            'gst_number'=>'nullable|string|max:15',
            'address'=>'required|string|max:255',
            
        ];
    }
}
