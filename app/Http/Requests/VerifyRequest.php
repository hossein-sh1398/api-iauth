<?php

namespace App\Http\Requests;

use App\Rules\MobileRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mobile' => ['bail', 'required', new MobileRule(), 'exists:users,mobile'],
            'otp_code' => ['bail', 'required', 'numeric', 'digits:4'],
        ];
    }
}
