<?php

namespace App\Repositories;
use App\Models\Invite;

use App\Constants\Invite as InviteConstants;
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
            ->where('status', InviteConstants::INVITE_PENDING)
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
            ->where('status', InviteConstants::INVITE_PENDING)
            ->where('inviting_company_id', $inviting_company_id)
            ->where('invite_expire', '>', now())
            ->first();
    }

    public function update(Invite $invite, array $data): ?Invite
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
}