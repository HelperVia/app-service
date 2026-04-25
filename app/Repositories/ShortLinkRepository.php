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
    public function deleteBy(array $where): bool
    {
        return $this->shortLinkModel::query()->where($where)->delete() > 0;


    }
    public function getInviteByShortToken(string $short_token): ?ShortLink
    {
        return $this->shortLinkModel->with('invite')->where('short_token', $short_token)
            ->where('type', 'invite')->first();
    }


}