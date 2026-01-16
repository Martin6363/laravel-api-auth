<?php

namespace Martin6363\ApiAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
        $minLength = config('api-auth.password.min_length', 8);
        $requireConfirmation = config('api-auth.password.require_confirmation', true);

        $rules = [
            'token' => ['required', 'string'],
            'email' => array_merge(
                config('api-auth.validation.email', ['required', 'string', 'email', 'max:255']),
                ['exists:' . $tableName . ',email']
            ),
        ];

        $passwordRules = ['required', 'string', 'min:' . $minLength];
        
        if ($requireConfirmation) {
            $passwordRules[] = 'confirmed';
        }

        $rules['password'] = $passwordRules;

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'token.required' => __('validation.required', ['attribute' => 'token']),
            'email.required' => __('validation.required', ['attribute' => 'email']),
            'email.email' => __('validation.email', ['attribute' => 'email']),
            'email.exists' => __('validation.exists', ['attribute' => 'email']),
            'password.required' => __('validation.required', ['attribute' => 'password']),
            'password.min' => __('validation.min.string', [
                'attribute' => 'password',
                'min' => config('api-auth.password.min_length', 8),
            ]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => 'password']),
        ];
    }
}