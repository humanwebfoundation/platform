<?php

namespace App\Http\Requests\Developer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOAuthClientRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'redirect_url' => ['required', 'url', 'max:2000'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateRedirectUrl($validator);
        });
    }

    /**
     * Validate redirect URL for security concerns.
     */
    protected function validateRedirectUrl(Validator $validator): void
    {
        $url = $this->input('redirect_url');

        if (!$url) {
            return;
        }

        $parsed = parse_url($url);

        // Block javascript: and data: URLs
        if (isset($parsed['scheme']) && in_array(strtolower($parsed['scheme']), ['javascript', 'data', 'file'])) {
            $validator->errors()->add('redirect_url', 'Invalid URL scheme.');
            return;
        }

        // Block URLs with fragments (potential open redirect via fragment)
        if (isset($parsed['fragment'])) {
            $validator->errors()->add('redirect_url', 'URL cannot contain fragments (#).');
            return;
        }

        // In production, enforce HTTPS (except for localhost for testing)
        if (app()->environment('production')) {
            $host = $parsed['host'] ?? '';
            $scheme = $parsed['scheme'] ?? '';

            $isLocalhost = in_array($host, ['localhost', '127.0.0.1', '::1']);

            if (!$isLocalhost && strtolower($scheme) !== 'https') {
                $validator->errors()->add('redirect_url', 'Redirect URL must use HTTPS in production.');
            }
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide a name for your application.',
            'redirect_url.required' => 'A redirect URL is required for OAuth authentication.',
            'redirect_url.url' => 'Please provide a valid URL for the redirect.',
        ];
    }
}
