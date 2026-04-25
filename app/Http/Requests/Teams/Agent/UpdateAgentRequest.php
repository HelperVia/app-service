<?php

namespace App\Http\Requests\Teams\Agent;


use App\Domain\Agent\Constants\Agent;
use App\Rules\MongoObjectId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateAgentRequest extends FormRequest
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
            'away' => ['sometimes', 'required', Rule::in([Agent::AGENT_AWAY_ENABLE, Agent::AGENT_AWAY_DISABLE])],
            'chat_limit' => 'sometimes|required|integer|between:0,50',
            'agent_name' => 'sometimes|required|string|max:50|min:3',
            'job_title' => 'sometimes|required|string|max:50|min:3',
            'auto_assign' => ['sometimes', 'required', Rule::in([Agent::AGENT_AUTO_ASSIGN_ENABLE, Agent::AGENT_AUTO_ASSIGN_DISABLE])],
            'department_ids' => ['sometimes', 'array'],
            'department_ids.*' => new MongoObjectId,
        ];
    }
}
