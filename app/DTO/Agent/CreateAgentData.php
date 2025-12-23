<?php

namespace App\DTO\Agent;
use App\Constants\Agent;
use App\Constants\YesNo;
use App\Services\DepartmentService;
use Illuminate\Support\Str;
use App\Contracts\DTO\DtoInterface;
use App\DTO\Agent\Validatable\Validate;
class CreateAgentData extends Validate implements DtoInterface
{


    public function __construct(
        public readonly string $license_number,
        public ?string $user_id,
        public string $role,

        public ?string $agent_name = null,
        public ?array $department = null,
        public string $status = Agent::AGENT_STATUS_ACTIVE,
        public string $away = Agent::AGENT_AWAY_DISABLE,
        public int $active_chat = 0,
        public string $auto_assign = YesNo::YES,
        public ?int $chat_limit = null
    ) {
        $this->validate();
        $this->chat_limit ??= config('settings.default_agent_chat_limit');
        $this->user_id ??= Str::uuid();
        $this->agent_name ??= "Agent " . rand(0, 10);

        if ($this->department === null) {
            $departmentService = app(DepartmentService::class);
            $department = $departmentService->getDefaultDepartment($license_number);
            if (!$department) {
                throw new \Exception('Default department not found', 404);
            }
            $this->department = [$department->id => 1];
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