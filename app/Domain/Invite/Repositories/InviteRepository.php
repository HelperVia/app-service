<?php

namespace App\Domain\Invite\Repositories;
use App\Models\Companies;
use App\Models\Invite;

use App\Domain\Invite\Constants\InviteStatus;
class InviteRepository
{


    public function __construct(private Invite $inviteModel)
    {
    }

    public function findByInviteCode($invite_code): ?Invite
    {
        return $this->inviteModel->with(['company', 'user', 'shortLink'])
            ->whereHas('shortLink', function ($query) use ($invite_code) {
                $query->where('short_token', $invite_code)
                    ->where('type', 'invite');
            })
            ->where('invite_expire', '>', now()->timestamp)
            ->where('status', InviteStatus::INVITE_PENDING)
            ->whereHas('company')
            ->first();

    }
    public function create(array $data): Invite
    {
        return $this->inviteModel->create($data);
    }

    public function getActiveInviteByCompanyAndEmail(
        string $inviting_company_id,
        string $email
    ): ?Invite {
        return $this->inviteModel
            ->where('invited_email', $email)
            ->where('status', InviteStatus::INVITE_PENDING)
            ->where('inviting_company_id', $inviting_company_id)
            ->where('invite_expire', '>', now())
            ->first();
    }

    public function save(Invite $invite, array $data): ?Invite
    {
        $invite->fill($data);

        if ($invite->isDirty()) {
            $invite->save();
            return $invite->fresh();
        }

        return null;
    }
    public function changeStatus(Invite $invite, string $status)
    {
        $invite->status = $status;
        $invite->saveOrFail();

    }
    public function getInviteWithCompany(
        ?array $inviteWhere = null,
        ?array $companyWhere = null
    ): ?Invite {
        $query = $this->inviteModel
            ->with('company');

        if (!empty($inviteWhere)) {
            $query->where($inviteWhere);
        }

        if (!empty($companyWhere)) {
            $query->whereHas('company', function ($q) use ($companyWhere) {
                $q->where($companyWhere);
            });
        }

        return $query->first();
    }
    public function deleteBy($where): bool
    {
        return $this->inviteModel->where($where)->delete() > 0;
    }
    public function delete(string $id): bool
    {
        return $this->inviteModel
            ->where('id', $id)
            ->delete() > 0;
    }
}