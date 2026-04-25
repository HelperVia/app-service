<?php

namespace App\Domain\Invite\DTO;
use App\Domain\Invite\Constants\InviteStatus;


class ChangeInviteStatusData
{


    public function __construct(
        public readonly string $status,

    ) {
        $this->validate();
    }

    private function validate()
    {
        $validStatuses = [
            InviteStatus::INVITE_COMPLETE,
            InviteStatus::INVITE_DECLINE,
            InviteStatus::INVITE_EXPIRED,
            InviteStatus::INVITE_PENDING
        ];

        if (!in_array($this->status, $validStatuses, true)) {
            throw new \InvalidArgumentException(
                "Invalid invite status '{$this->status}'. Valid statuses: " . implode(", ", $validStatuses)
            );
        }


    }
}