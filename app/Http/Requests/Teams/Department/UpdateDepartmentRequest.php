<?php

namespace App\Http\Requests\Teams\Department;

use App\Rules\MongoObjectId;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
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
            'id' => ['required', new MongoObjectId],
            'department_name' => 'sometimes|required|string|min:3|max:50',
            'agent_ids' => 'sometimes|array',
            'agent_ids.*' => new MongoObjectId
        ];
    }
}
