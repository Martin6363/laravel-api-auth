<?php

namespace Vendor\ApiAuth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as PasswordRule;

class RegisterRequest extends FormRequest
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
        
        // Get all validation rules from config
        $validationRules = config('api-auth.validation', []);
        
        $rules = [];
        
        // Process each field from config
        foreach ($validationRules as $field => $fieldRules) {
            if ($field === 'password') {
                // Handle password separately with special rules
                $passwordRules = ['required', 'string', 'min:' . $minLength];
                
                if ($requireConfirmation) {
                    $passwordRules[] = 'confirmed';
                }
                
                $rules['password'] = $passwordRules;
            } elseif ($field === 'email') {
                // Add unique rule to email for registration
                $rules['email'] = array_merge(
                    is_array($fieldRules) ? $fieldRules : [$fieldRules],
                    ['unique:' . $tableName . ',email']
                );
            } else {
                // Use rules as-is for all other fields
                $rules[$field] = is_array($fieldRules) ? $fieldRules : [$fieldRules];
            }
        }
        
        // Ensure required fields exist even if not in config
        if (!isset($rules['name'])) {
            $rules['name'] = ['required', 'string', 'max:255'];
        }
        
        if (!isset($rules['email'])) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:' . $tableName . ',email'];
        }
        
        if (!isset($rules['password'])) {
            $passwordRules = ['required', 'string', 'min:' . $minLength];
            if ($requireConfirmation) {
                $passwordRules[] = 'confirmed';
            }
            $rules['password'] = $passwordRules;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [];
        $validationRules = config('api-auth.validation', []);
        $minLength = config('api-auth.password.min_length', 8);
        
        // Generate messages for all configured fields
        foreach (array_keys($validationRules) as $field) {
            if ($field === 'password') {
                $messages['password.required'] = __('validation.required', ['attribute' => 'password']);
                $messages['password.min'] = __('validation.min.string', [
                    'attribute' => 'password',
                    'min' => $minLength,
                ]);
                $messages['password.confirmed'] = __('validation.confirmed', ['attribute' => 'password']);
            } elseif ($field === 'email') {
                $messages['email.required'] = __('validation.required', ['attribute' => 'email']);
                $messages['email.email'] = __('validation.email', ['attribute' => 'email']);
                $messages['email.unique'] = __('validation.unique', ['attribute' => 'email']);
            } else {
                // Generic messages for custom fields
                $messages[$field . '.required'] = __('validation.required', ['attribute' => $field]);
            }
        }
        
        // Ensure default messages exist
        $defaultMessages = [
            'name.required' => __('validation.required', ['attribute' => 'name']),
            'email.required' => __('validation.required', ['attribute' => 'email']),
            'email.email' => __('validation.email', ['attribute' => 'email']),
            'email.unique' => __('validation.unique', ['attribute' => 'email']),
            'password.required' => __('validation.required', ['attribute' => 'password']),
            'password.min' => __('validation.min.string', [
                'attribute' => 'password',
                'min' => $minLength,
            ]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => 'password']),
        ];
        
        return array_merge($defaultMessages, $messages);
    }
}