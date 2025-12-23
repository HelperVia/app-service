<?php

namespace App\DTO\Agent;
use App\DTO\Agent\Validatable\Validate;
use App\Contracts\DTO\DtoInterface;
use App\Traits\DtoToArray;
class UpdateAgentData extends Validate implements DtoInterface
{

    use DtoToArray;
    public function __construct(
        public readonly ?string $license_number = null,
        public readonly ?string $user_id = null,
        public readonly ?string $role = null,
        public readonly ?string $agent_name = null,
        public readonly ?array $department = null,
        public readonly ?string $status = null,
        public readonly ?string $away = null,
        public readonly ?int $active_chat = null,
        public readonly ?string $auto_assign = null,
        public readonly ?int $chat_limit = null
    ) {
        $this->validate();
    }



    public function validate(): void
    {

        if ($this->status != null) {
            $this->validateStatus($this->status);
        }

        if ($this->role != null) {
            $this->validateRoles($this->role);
        }

        if ($this->away != null) {
            $this->validateAway($this->away);
        }

        if ($this->auto_assign != null) {
            $this->validateAutoAssign($this->auto_assign);
        }

        if ($this->chat_limit !== null) {
            $this->validateChatLimit($this->chat_limit);
        }

        if ($this->active_chat !== null) {
            $this->validateActiveChat($this->active_chat);
        }

    }


}