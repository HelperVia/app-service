<?php

namespace App\Http\Requests\Teams\Department;

use App\Rules\MongoObjectId;
use Illuminate\Foundation\Http\FormRequest;

class CreateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'department_name' => mb_convert_case($this->department_name, MB_CASE_TITLE, 'UTF-8')
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
            'department_name' => 'required|min:3|max:50',
            'agent_ids' => 'sometimes|array',
            'agent_ids.*' => new MongoObjectId
        ];
    }
}
