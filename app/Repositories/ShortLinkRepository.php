<?php

namespace App\Repositories;

use App\Models\ShortLink;


class ShortLinkRepository
{

    public function __construct(private readonly ShortLink $shortLinkModel)
    {
    }

    public function create(array $data): ShortLink
    {
        return $this->shortLinkModel->create($data);
    }
    public function getTargetByShortTokenAndType($target, $type): ?ShortLink
    {

        return $this->shortLinkModel->where('short_token', $target)
            ->where('type', $type)->first();
    }
    public function deleteByShortToken(string $short_token)
    {
        return $this->shortLinkModel->where('short_token', $short_token)->delete();
    }
    public function getInviteByShortToken(string $short_token): ?ShortLink
    {
        return $this->shortLinkModel->with('invite')->where('short_token', $short_token)
            ->where('type', 'invite')->first();
    }


}