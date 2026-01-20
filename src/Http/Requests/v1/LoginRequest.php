<?php

namespace Martin6363\ApiAuth\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
        return config('api-auth.login.fields', [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $rules = $this->rules();
        $messages = [];

        foreach ($rules as $field => $fieldRules) {
            // Setting up messages for each rule of the field
            $fieldRulesArray = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

            // Loop through each rule for the field
            foreach ($fieldRulesArray as $rule) {
                // Remove parameters from rule (e.g., min:8 -> min)
                $ruleName = explode(':', $rule)[0];

                $messages["$field.$ruleName"] = __("validation.$ruleName", [
                    'attribute' => $field,
                ]);
            }
        }

        return $messages;
    }
}