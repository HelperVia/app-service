<?php

namespace App\Http\Requests\Customer;

use App\Rules\ValidUUID;
use Illuminate\Foundation\Http\FormRequest;

class CreateTokenRequest extends FormRequest
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
            'company_id' => ['required', new ValidUUID]
        ];
    }
}
