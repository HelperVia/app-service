<?php

namespace App\Services;

use App\Repositories\CannedResponseRepository;
use Illuminate\Support\Str;

class CannedResponseService
{

    public function __construct(private CannedResponseRepository $cannedResponseRepository)
    {
    }


    public function truncate(string $tenant = '')
    {
        return $this->cannedResponseRepository->truncate($tenant);
    }
    public function create(array $data, string $tenant = '')
    {

        return $this->cannedResponseRepository->create([
            'shortcut' => $data['shortcuts'],
            'content' => $data['content'],
            'global_id' => (string) Str::uuid()
        ], $tenant);

    }



}