<?php

namespace App\Http\Requests\Teams\Agent\Invite;

use App\Rules\MongoObjectId;
use Illuminate\Foundation\Http\FormRequest;

class CancelInviteRequest extends FormRequest
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
            'id' => $this->route('id')
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
            'id' => ['required', new MongoObjectId]
        ];
    }
}
