<?php

namespace App\Observers;

use App\Models\Settings;
use Cache;

class SettingsObserver
{


    private function clearCache(Settings $settings)
    {
        Cache::forget($settings->tenant . ":settings");
    }
    /**
     * Handle the Settings "created" event.
     */
    public function created(Settings $settings): void
    {
        $this->clearCache($settings);
    }

    /**
     * Handle the Settings "updated" event.
     */
    public function updated(Settings $settings): void
    {

        $this->clearCache($settings);
    }

    /**
     * Handle the Settings "deleted" event.
     */
    public function deleted(Settings $settings): void
    {
        $this->clearCache($settings);
    }

    /**
     * Handle the Settings "restored" event.
     */
    public function restored(Settings $settings): void
    {
        $this->clearCache($settings);
    }

    /**
     * Handle the Settings "force deleted" event.
     */
    public function forceDeleted(Settings $settings): void
    {
        $this->clearCache($settings);
    }
}
