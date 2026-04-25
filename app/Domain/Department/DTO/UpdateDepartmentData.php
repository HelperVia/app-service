<?php

namespace App\Domain\Department\DTO;



use App\Domain\Department\Enums\DepartmentDefaultFlag;
use App\Domain\Department\Enums\DepartmentStatus;
use App\Services\Token\DepartmentKeyService;
use App\Traits\DtoToArray;


class UpdateDepartmentData
{

    use DtoToArray;

    public function __construct(
        public ?string $department_name = null,
        public readonly ?DepartmentDefaultFlag $default = null,
        public readonly ?DepartmentStatus $status = null,
        public ?string $department_key = null,
    ) {
        if (!empty($this->department_name)) {
            $this->department_name = mb_convert_case($this->department_name, MB_CASE_TITLE, 'UTF-8');
            $this->department_key ??= app(DepartmentKeyService::class)->encode(['department_name' => $this->department_name]);

        }

    }
}