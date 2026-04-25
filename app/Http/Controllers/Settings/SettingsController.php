<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Settings\DTO\UpdateSettingsData;
use App\Domain\Settings\Services\SettingService;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    public function __construct(private readonly SettingService $settingService)
    {
    }
    public function update(UpdateSettingsRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $license_id = $user->license;

        $settingsData = new UpdateSettingsData(
            prechatform: $validated['prechatform'] ?? null,
            postchatform: $validated['postchatform'] ?? null,
            widget_customization: $validated['widget_customization'] ?? null,
            widget_language: $validated['widget_language'] ?? null,
        );
        $updated = $this->settingService->update($license_id, $settingsData);

        if ($updated === false) {
            throw new ApiException('Unable to save your changes. Please try again.', 422);
        }
        $data = array_filter([
            'prechatform' => $validated['prechatform'] ?? null,
            'postchatform' => $validated['postchatform'] ?? null,
            'widget_customization' => $validated['widget_customization'] ?? null,
            'widget_language' => $validated['widget_language'] ?? null,
        ]);
        return response()->success($data, "Settings updated successfully.");


    }
}
