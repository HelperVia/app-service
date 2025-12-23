<?php

namespace App\Repositories;

use App\Models\CannedResponse;

class CannedResponseRepository
{

    public function __construct(private CannedResponse $model)
    {
    }

    public function create(array $data, string $tenant = '')
    {
        return $this->model->tenant($tenant)->create($data);
    }

    public function truncate(string $tenant = '')
    {
        return $this->model->tenant($tenant)->truncate();
    }


}