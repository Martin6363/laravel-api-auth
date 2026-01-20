<?php

namespace Martin6363\ApiAuth\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $userModel = config('api-auth.user_model');
        $tableName = (new $userModel)->getTable();

        return [
            'email' => array_merge(
                config('api-auth.validation.email', ['required', 'string', 'email', 'max:255']),
                ['exists:' . $tableName . ',email']
            ),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => __('validation.required', ['attribute' => 'email']),
            'email.email' => __('validation.email', ['attribute' => 'email']),
            'email.exists' => __('validation.exists', ['attribute' => 'email']),
        ];
    }
}