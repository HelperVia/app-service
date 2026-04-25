<?php

namespace App\Domain\Department\Repositories;

interface DepartmentRepositoryInterface
{
    public function getWithAgents(string $tenant, string $status): array;
    public function getDefaultDepartment(string $tenant): array;
    public function createOrUpdateByKey(array $data, string $department_key, string $tenant): array;

}