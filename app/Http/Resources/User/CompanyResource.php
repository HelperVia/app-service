<?php

namespace App\Http\Resources\User;

use App\Constants\YesNo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Domain\Agent\Constants\Agent;
class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'license_number' => $this->license_number,
            'company_name' => $this->company_name,
            'owner' => $this->pivot->role == Agent::AGENT_ROLE_OWNER ? YesNo::YES : YesNo::NO,
            'created_at' => $this->pivot->role == Agent::AGENT_ROLE_OWNER ? $this->created_at : $this->pivot_created_at,
        ];
    }
}
