<?php

namespace App\Domain\Agent\DTO;
use App\Domain\Agent\DTO\Validatable\Validate;
use App\Contracts\DTO\DtoInterface;
use App\Rules\MongoObjectId;
use App\Traits\DtoToArray;
class ModifyAgentDepartmentData extends Validate implements DtoInterface
{

    use DtoToArray;
    public function __construct(
        public array|string $department_id,
        public bool $get_default = true

    ) {

        $this->validate();


    }



    public function validate(): void
    {
        $this->validateDepartmentIds($this->department_id, $this->get_default);
    }


}