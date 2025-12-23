<?php

namespace App\Repositories;
use App\Models\Department;
class DepartmentRepository
{

    public function __construct(private Department $model)
    {
    }

    public function create(string $tenant, array $data): Department
    {
        return $this->model->tenant($tenant)->create($data);
    }
    public function getDefaultDepartment(string $tenant): ?Department
    {
        return $this->model->tenant($tenant)->where('default', true)->first();
    }
}