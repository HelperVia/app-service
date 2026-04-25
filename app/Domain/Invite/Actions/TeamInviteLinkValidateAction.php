<?php

namespace App\Domain\Invite\Actions;

use App\Exceptions\ApiException;
use App\Models\Invite;
use App\Services\InviteService;
use App\Services\ShortLinkService;
use App\Services\Token\InviteTokenService;
use App\Domain\Agent\Constants\Agent;


class TeamInviteLinkValidateAction
{


    public function __construct(
        private readonly InviteTokenService $inviteTokenService,
        private readonly ShortLinkService $shortLinkService,
        private readonly InviteService $inviteService,

    ) {
    }

    public function execute(array $data, bool $returnRaw = false): array|Invite
    {
        $this->validateField($data);

        $invite = $this->validateCode($data['code']);

        if (empty($invite)) {
            $this->exceptionHandler();
        }
        if ($returnRaw) {
            return $invite;
        }
        return [
            'invited_email' => $invite->invited_email,
            'full_name' => $invite->temporary_name,
            'invited_role' => $invite->invited_role,
            'invited_role_description' => Agent::getRoleLabel($invite->invited_role),
            'company' => $invite->company->company_name,
            'has_account' => (bool) $invite->user
        ];

    }

    private function validateField(array $data): void
    {
        if (empty($data['code'])) {
            $this->exceptionHandler();
        }
    }

    private function validateCode(string $code): ?Invite
    {
        try {
            return $this->inviteService->validate($code);
        } catch (ApiException $e) {
            $this->ExceptionHandler();
        }
    }

    private function exceptionHandler(): never
    {
        throw new ApiException('The invite code you provided is invalid or has expired.');

    }

}