<?php

namespace App\Http\Requests\Init;

use Illuminate\Foundation\Http\FormRequest;

class ConnectionTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'device_type' => $this->header('X-Device-Type'),
            'cookie_name' => $this->header('X-Cookie-Name')
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'device_type' => ['required', 'in:browser'],
            'cookie_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^hv:[a-zA-Z0-9_-]+:connection-token$/'
            ]
        ];
    }
}
