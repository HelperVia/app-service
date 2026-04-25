<?php

namespace App\Domain\Agent\Actions;

use App\Domain\Agent\DTO\UpdateAgentData;
use App\Exceptions\ApiException;
use App\Models\Agent;
use App\Models\User;
use App\Domain\Agent\Services\AgentService;
use App\Services\UserService;

use Throwable;
use DB;

class DeleteAgentAction
{


    public function __construct(
        private readonly AgentService $agentService,
        private readonly UserService $userService
    ) {

    }

    public function execute(string $id, int $license = null): array
    {
        try {


            DB::connection('mongodb')->beginTransaction();
            DB::beginTransaction();

            $license = empty($license) ? auth()->user()->license : $license;
            $agent = $this->getAgent($id, $license);
            $this->detachCompany($agent->user_id, $agent->license_number);
            $this->updateAgent($agent);

            DB::connection('mongodb')->commit();
            DB::commit();
            return [
                'agent' => $agent
            ];

        } catch (Throwable $e) {
            report($e);
            DB::connection('mongodb')->rollBack();
            DB::rollBack();

            throw $e;
        }
    }

    private function updateAgent($agent)
    {


        $agentData = new UpdateAgentData(
            status: \App\Domain\Agent\Constants\Agent::AGENT_STATUS_DELETED,
        );

        $deleted = $this->agentService->updateAgentByUserID(
            $agent->license_number,
            $agent->user_id,
            $agentData,
        );

        if (!$deleted) {
            throw new ApiException('Agent status could not be updated to ' . \App\Domain\Agent\Constants\Agent::AGENT_STATUS_DELETED . '.', 422);
        }



    }

    private function getAgent(string $id, int $license): ?Agent
    {
        $agent = $this->agentService->getActiveAgentByID($license, $id);
        if ($agent === null) {
            throw new ApiException('Agent to be deleted not found.', 422);
        }
        if ($agent->role === \App\Domain\Agent\Constants\Agent::AGENT_ROLE_OWNER) {
            throw new ApiException('Deletion failed: the agent is an owner and cannot be removed', 422);
        }
        return $agent;
    }
    private function detachCompany(string $user_id, string $license_number): ?User
    {


        $user = $this->userService->find($user_id);
        if (!$user) {
            throw new ApiException('The agent could not be deleted.', 422);
        }

        $detachCompany = $this->userService->detachCompanyByLicenseNumber($user, $license_number);

        if (!$detachCompany) {
            throw new ApiException('The agent could not be deleted.', 422);
        }

        return $user;

    }



}