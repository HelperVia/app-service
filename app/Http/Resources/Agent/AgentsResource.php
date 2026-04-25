<?php

namespace App\Http\Resources\Agent;

use App\Domain\Agent\Constants\Agent;
use App\Constants\YesNo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'agent_name' => $this->agent_name ?? "",
            'email' => $this->email ?? "",
            'status' => $this->status,
            'department_ids' => $this->department_ids,
            'invited' => $this->invited ?? YesNo::NO,
            'away' => $this->away,
            'active_chat' => $this->active_chat,
            'chat_limit' => $this->chat_limit,
            'auto_assign' => $this->auto_assign,
            'role' => $this->role,
            'role_description' => Agent::getRoleLabel($this->role),
            'created_at' => $this->created_at,
            'source' => $this->source,
            'job_title' => $this->job_title ?? ""
        ];
    }
}
