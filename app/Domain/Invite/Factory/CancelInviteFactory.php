<?php

namespace App\Domain\Invite\Factory;
use Illuminate\Contracts\Container\Container;
use App\Domain\Invite\Actions\CancelInviteByAgent;

class CancelInviteFactory
{

    public function __construct(readonly private Container $container)
    {
    }
    public function make(string $type)
    {

        return match ($type) {
            'agent' => $this->container->make(CancelInviteByAgent::class),
            default => throw new \InvalidArgumentException('Invalid cancel type'),
        };
    }
}