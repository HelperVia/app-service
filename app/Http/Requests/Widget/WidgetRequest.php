<?php

namespace App\Http\Requests\Widget;

use App\Rules\ValidLicenseId;
use Illuminate\Foundation\Http\FormRequest;

class WidgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'license_id' => $this->route('license_id')
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
            'callback' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z_$][a-zA-Z0-9_$]*$/'],
            'license_id' => new ValidLicenseId
        ];
    }
}
