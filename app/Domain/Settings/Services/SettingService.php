<?php

namespace App\Domain\Settings\Services;



use App\Exceptions\ApiException;
use App\Repositories\AgentRepository;
use App\Repositories\SettingRepository;


class SettingService
{



    public function __construct(private SettingRepository $settingRepository)
    {
    }

    public function getAllSettings(string $tenant)
    {
        return $this->settingRepository->getAllSettings($tenant);
    }

    public function update(string $tenant, $data)
    {
        $fillData = [];

        if ($data->prechatform !== null) {
            $fillData['prechatform'] = $data->prechatform;
        }

        if ($data->postchatform !== null) {
            $fillData['postchatform'] = $data->postchatform;
        }

        if ($data->widget_customization !== null) {
            $fillData['widget.customization'] = $data->widget_customization;
        }
        if ($data->widget_language !== null) {
            $fillData['widget.languages'] = $data->widget_language;
        }


        return $this->settingRepository->update($tenant, $fillData);
    }
    public function create(string $tenant, $data)
    {

        $fillData = [];

        if ($data->prechatform !== null) {
            $fillData['prechatform'] = $data->prechatform;
        }

        if ($data->postchatform !== null) {
            $fillData['postchatform'] = $data->postchatform;
        }

        if ($data->widget_customization !== null) {
            $fillData['widget.customization'] = $data->widget_customization;
        }
        if ($data->widget_language !== null) {
            $fillData['widget.languages'] = $data->widget_language;
        }

        return $this->settingRepository->create($tenant, $fillData);
    }



}