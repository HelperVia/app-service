<?php

namespace App\Domain\Department\Services;


use App\Domain\Agent\Constants\Agent;
use App\Domain\Department\DTO\CreateDepartmentData;
use App\Domain\Department\DTO\UpdateDepartmentData;
use App\Domain\Department\Enums\DepartmentDefaultFlag;
use App\Domain\Department\Enums\DepartmentStatus;
use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use Throwable;



class DepartmentService
{

    public function __construct(
        private DepartmentRepositoryInterface $repository,

    ) {
    }

    public function createOrUpdateByKey(CreateDepartmentData $data, string $tenant): array
    {

        try {
            return $this->repository->createOrUpdateByKey($data->toArray(), $data->department_key, $tenant);

        } catch (\Illuminate\Database\QueryException $e) {

            throw new \RuntimeException("Department could not be saved: " . $e->getMessage());
        }

    }

    public function createDefaultDepartment(string $tenant, string $lang = 'en'): array
    {
        $default_departments = config('departments');
        $default_department = $default_departments[$lang] ?? $default_departments['en'];
        $data = new CreateDepartmentData(
            department_name: $default_department['department_name'],
            default: DepartmentDefaultFlag::DEFAULT
        );
        return $this->createOrUpdateByKey($data, $tenant);
    }

    public function getDefaultDepartment(string $tenant): array
    {
        return $this->repository->getDefaultDepartment($tenant);
    }


    public function filterNonDeletedAgents(array $departments): array
    {
        foreach ($departments as &$department) {
            $department['agents'] = array_values(array_filter(
                $department['agents'] ?? [],
                fn($agent) => !in_array($agent['status'] ?? '', [
                    Agent::AGENT_STATUS_DELETED,
                    Agent::AGENT_STATUS_CANCELED
                ])
            ));
        }

        unset($department);
        return $departments;
    }

    public function getWithAgents(string $tenant, array $department_ids = []): array
    {
        return $this->filterNonDeletedAgents($this->repository->getWithAgents($tenant, DepartmentStatus::DELETED->value, $department_ids));
    }

    public function deleteDepartmentById(string $id, string $tenant): bool
    {
        return $this->repository->update(
            ['status' => DepartmentStatus::DELETED],
            [
                '_id' => $id,
                'default' => DepartmentDefaultFlag::NOT_DEFAULT,
                'status' => DepartmentStatus::ACTIVE
            ],
            $tenant
        );
    }

    public function availableDepartmentById(string $tenant, array|string $ids, bool $get_default = true): array
    {
        return $this->repository
            ->availableDepartmentById(
                $tenant,
                DepartmentStatus::DELETED->value,
                $ids,
                DepartmentDefaultFlag::DEFAULT ->value,
                $get_default
            );
    }

    public function findById(string $tenant, string $id): array
    {
        return $this->repository->findById($tenant, $id);
    }
    public function updateById(string $tenant, string $id, UpdateDepartmentData $data): bool
    {
        return $this->repository->updateById($tenant, $id, $data->toArray());

    }

}

