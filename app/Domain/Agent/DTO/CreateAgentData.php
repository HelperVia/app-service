<?php

namespace App\Domain\Agent\DTO;
use App\Domain\Agent\Constants\Agent;
use App\Domain\Department\Services\DepartmentService;
use Illuminate\Support\Str;
use App\Contracts\DTO\DtoInterface;
use App\Domain\Agent\DTO\Validatable\Validate;
class CreateAgentData extends Validate implements DtoInterface
{


    public function __construct(
        public readonly string $license_number,
        public ?string $user_id,
        public string $role,

        public ?string $agent_name = null,
        public ?array $department_ids = [],
        public string $status = Agent::AGENT_STATUS_ACTIVE,
        public string $away = Agent::AGENT_AWAY_DISABLE,
        public int $active_chat = 0,
        public string $auto_assign = Agent::AGENT_AUTO_ASSIGN_ENABLE,
        public ?int $chat_limit = null,
        public ?string $job_title = null
    ) {
        $this->validate();
        $this->chat_limit ??= config('settings.default_agent_chat_limit');
        $this->user_id ??= Str::uuid();
        $this->agent_name ??= "Agent " . rand(0, 10);

        if (count($this->department_ids) < 1) {
            $departmentService = app(DepartmentService::class);
            $department = $departmentService->getDefaultDepartment($license_number);

            if (!$department) {
                throw new \Exception('Default department not found', 404);
            }

            $this->department_ids[] = $department['id'];
        }
    }

    public function validate(): void
    {

        $this->validateStatus($this->status);
        $this->validateRoles($this->role);
        $this->validateAway($this->away);
        $this->validateAutoAssign($this->auto_assign);
        $this->validateChatLimit($this->chat_limit);
        $this->validateActiveChat($this->active_chat);



    }
}