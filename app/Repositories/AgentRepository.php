<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Constants\Agent as AgentConstants;
class AgentRepository
{


    public function __construct(private Agent $model)
    {
    }

    public function create(string $tenant, array $data): Agent
    {
        return $this->model->tenant($tenant)->create($data);
    }
    public function getAgents(string $tenant)
    {
        return $this->model->tenant($tenant)->where('status', '!=', AgentConstants::AGENT_STATUS_DELETED)->get();
    }
    public function update(string $tenant, array $data, array $where): ?Agent
    {
        $agent = $this->model->tenant($tenant)->where($where)->first();

        if (!$agent) {
            return null;
        }
        $agent->fill($data);
        if ($agent->isDirty()) {
            $agent->save();
            return $agent->fresh();
        }
        return $agent;
    }

    public function updateOrInsertByUserID(string $tenant, array $data, string $user_id): Agent
    {
        return $this->model->tenant($tenant)->updateOrCreate(['user_id' => $user_id], $data);
    }



}