<?php

namespace App\Actions\Utils;

use App\DTO\Utils\CreateShortLinkData;
use App\Exceptions\ApiException;
use App\Services\ShortLinkService;

class CreateShortLinkAction
{
    public function __construct(
        private readonly ShortLinkService $shortLinkService,
    ) {
    }

    public function execute(array $data)
    {
        $this->validateField($data);

        $shortLinkData = new CreateShortLinkData(
            target: $data['target'],
            type: $data['type'],
            short_token: $data['short_token'] ?? null
        );
        return $this->shortLinkService->create($shortLinkData);
    }

    private function validateField(array $data)
    {
        $missing = [];
        if (!isset($data['target']) || empty($data['target'])) {
            $missing[] = 'target';
        }
        if (!isset($data['type']) || empty($data['type'])) {
            $missing[] = 'type';
        }

        if (!empty($missing)) {
            throw new ApiException(
                'Missing or invalid field(s): ' . implode(', ', $missing)
            );
        }
    }
}