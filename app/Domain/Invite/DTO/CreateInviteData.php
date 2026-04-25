<?php

namespace App\Domain\Invite\DTO;
use App\Services\Token\InviteTokenService;
use Illuminate\Support\Str;

class CreateInviteData
{


    public function __construct(
        public readonly string $inviting_company_id,
        public readonly string $invited_email,
        public readonly string $inviting_user,
        public readonly string $invited_role,
        public readonly string $temporary_name,

        public ?string $invited_id = null,
        public ?string $invite_expire = null,
        public ?string $invite_code = null,
        private ?InviteTokenService $inviteTokenService = null

    ) {
        $this->invited_id = $this->invited_id ?? Str::uuid();
        $this->invite_expire ??= strtotime('+5 days');
        $this->inviteTokenService ??= app(InviteTokenService::class);
        $this->invite_code ??= $this->inviteTokenService->encode(['email' => $this->invited_email, 'inviting_company_id' => $this->inviting_company_id]);

    }
}