<?php

namespace App\DTO\Invite;
use App\Constants\Invite;


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
            Invite::INVITE_COMPLETE,
            Invite::INVITE_DECLINE,
            Invite::INVITE_EXPIRED,
            Invite::INVITE_PENDING
        ];

        if (!in_array($this->status, $validStatuses, true)) {
            throw new \InvalidArgumentException(
                "Invalid invite status '{$this->status}'. Valid statuses: " . implode(", ", $validStatuses)
            );
        }


    }
}