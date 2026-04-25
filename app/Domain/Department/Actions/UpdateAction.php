<?php

namespace App\Domain\Department\Actions;

use App\Domain\Agent\DTO\ModifyAgentDepartmentData;
use App\Domain\Agent\Services\AgentService;
use App\Domain\Department\DTO\UpdateDepartmentData;
use App\Domain\Department\Enums\DepartmentDefaultFlag;
use App\Domain\Department\Services\DepartmentService;
use App\Exceptions\ApiException;
use DB;
use Log;
use Throwable;

class UpdateAction
{



    public function __construct(
        private readonly DepartmentService $departmentService,
        private readonly AgentService $agentService
    ) {
    }

    public function execute(string $tenant, string $id, array $data)
    {

        return DB::connection('mongodb')->transaction(function () use ($data, $tenant, $id) {

            try {


                $department = $this->getDepartment($tenant, $id);
                $this->update($tenant, $id, $data);


                if ($department['default'] == DepartmentDefaultFlag::NOT_DEFAULT->value) {
                    $this->modifyAgent($tenant, $id, $data['agent_ids'] ?? null);
                }

                return $department;

            } catch (Throwable $e) {

                Log::error('Failed to update department', [
                    'tenant' => $tenant,
                    'data' => $data,
                    'error' => $e->getMessage(),
                    'trace' => app()->isProduction() ? null : $e->getTraceAsString()
                ]);

                throw $e;
            }

        });


    }

    private function getDepartment(string $tenant, string $id): array
    {

        $department = $this->departmentService->findById($tenant, $id);
        if (empty($department)) {
            throw new ApiException('The requested department does not exist');
        }

        return $department;

    }
    private function modifyAgent(string $tenant, string $department_id, array $agent_ids = null)
    {

        if (is_array($agent_ids)) {



            if (count($agent_ids) > 0) {
                $old_agent_ids = $this->agentService->getAgentByDepartmentId($tenant, $department_id)
                    ->pluck('_id')
                    ->toArray();

                $to_remove = array_diff($old_agent_ids, $agent_ids);
                $to_add = array_diff($agent_ids, $old_agent_ids);

                if (!empty($to_remove)) {
                    $this->agentService->detachDepartmentFromAgents($tenant, $department_id, $to_remove);
                }
                if (!empty($to_add)) {
                    $data = new ModifyAgentDepartmentData($department_id, false);
                    $this->agentService->addDepartmentToAgents($tenant, $data, $to_add);
                }
            } else {
                $this->agentService->detachDepartmentFromAgents($tenant, $department_id, []);
            }


        }



    }

    private function update(string $tenant, string $id, array $data): bool
    {

        $dto = new UpdateDepartmentData(
            department_name: $data['department_name']
        );

        $updated = $this->departmentService->updateById($tenant, $id, $dto);

        if (!$updated) {
            throw new ApiException('test');
        }

        return $updated;
    }
}