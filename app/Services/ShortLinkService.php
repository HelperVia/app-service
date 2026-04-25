<?php

namespace App\Services;

use App\DTO\Utils\CreateShortLinkData;
use App\Models\ShortLink;
use App\Repositories\ShortLinkRepository;

class ShortLinkService
{

    public function __construct(private readonly ShortLinkRepository $shortLinkRepo)
    {
    }
    public function create(CreateShortLinkData $data): ShortLink
    {

        return $this->shortLinkRepo->create([
            'short_token' => $data->short_token,
            'target' => $data->target,
            'type' => $data->type
        ]);
    }

    public function getTargetByShortTokenAndType(string $target, string $type): ?ShortLink
    {
        return $this->shortLinkRepo->getTargetByShortTokenAndType($target, $type);
    }
    public function deleteByShortToken(string $short_token): bool
    {
        return $this->shortLinkRepo->deleteBy([
            'short_token' => $short_token
        ]);
    }
    public function deleteByTargetInvite(string $invite_code): bool
    {

        $where = [
            'target' => $invite_code,
            'type' => 'invite'
        ];


        return $this->shortLinkRepo->deleteBy($where);
    }
    public function getInviteByShortToken(string $short_token): ?ShortLink
    {
        return $this->shortLinkRepo->getInviteByShortToken($short_token);
    }
}