<?php

namespace App\Services;


use App\Domain\Invite\DTO\ChangeInviteStatusData;
use App\Domain\Invite\DTO\CreateInviteData;
use App\Domain\Invite\DTO\UpdateInviteData;
use App\Exceptions\ApiException;
use App\Models\Invite;
use App\Models\User;
use App\Domain\Invite\Repositories\InviteRepository;
use App\Services\Token\InviteTokenService;

class InviteService
{


    public function __construct(
        private InviteRepository $inviteRepository,
        private InviteTokenService $inviteToken
    ) {
    }

    public function findByInviteCode(string $code): ?Invite
    {
        return $this->inviteRepository->findByInviteCode($code);
    }


    public function validate(?string $inviteCode): ?Invite
    {
        if (!$inviteCode) {
            return null;
        }
        $invite = $this->findByInviteCode($inviteCode);



        if (!$invite) {
            throw new ApiException('Invalid Invite Code', 400);
        }
        return $invite;
    }
    public function getActiveInviteByCompanyAndEmail(string $inviting_company_id, string $email): ?Invite
    {
        return $this->inviteRepository->getActiveInviteByCompanyAndEmail($inviting_company_id, $email);
    }

    public function create(CreateInviteData $data): Invite
    {
        $data = [
            'inviting_company_id' => $data->inviting_company_id,
            'invited_email' => $data->invited_email,
            'invited_id' => $data->invited_id,
            'inviting_user' => $data->inviting_user,
            'invite_expire' => $data->invite_expire,
            'invited_role' => $data->invited_role,
            'temporary_name' => $data->temporary_name,
            'invite_code' => $data->invite_code,

        ];
        return $this->inviteRepository->create($data);
    }

    public function generateTemporaryName(string $email, ?User $user = null): string
    {
        if (!empty($user?->full_name)) {
            return $user->full_name;
        }

        $localPart = strstr($email, '@', true);

        return !empty($localPart) ? $localPart : 'Agent';
    }

    public function save(Invite $invite, UpdateInviteData $data): ?Invite
    {
        return $this->inviteRepository->save($invite, $data->toArray());
    }
    public function changeStatus(Invite $invite, ChangeInviteStatusData $data)
    {

        return $this->inviteRepository->changeStatus($invite, $data->status);
    }
    public function getInviteWithCompany(
        ?array $inviteWhere = null,
        ?array $companyWhere = null
    ) {
        return $this->inviteRepository->getInviteWithCompany($inviteWhere, $companyWhere);
    }

    public function deleteBy(array $where = null): bool
    {
        return $this->inviteRepository->deleteBy($where);
    }
    public function delete(string $id): bool
    {
        return $this->inviteRepository->delete($id);
    }

}