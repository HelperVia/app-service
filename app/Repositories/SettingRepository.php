<?php

namespace App\Repositories;



use App\Exceptions\ApiException;
use App\Models\Settings;

class SettingRepository
{


    public function __construct(private Settings $model)
    {
    }

    public function getAllSettings(string $tenant)
    {
        return $this->model->tenant($tenant)->first();
    }


    public function create(string $tenant, $data)
    {

        return $this->model
            ->tenant($tenant)->create($data);

    }

    public function update(string $tenant, $data)
    {

        $settings = $this->model
            ->tenant($tenant)
            ->first();
        $settings->tenant = $tenant;

        $settings->fill($data);
        if ($settings->isDirty()) {
            $settings->save();
            return $settings->fresh();
        }

        return false;

    }





}