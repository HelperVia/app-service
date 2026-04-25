<?php

namespace App\Providers;

use App\Domain\Department\Repositories\DepartmentRepositoryInterface;
use App\Models\Settings;
use App\Observers\SettingsObserver;
use App\Repositories\DepartmentRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Carbon\CarbonInterval;
use Response;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            DepartmentRepositoryInterface::class,
            DepartmentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Settings::observe(SettingsObserver::class);
        Passport::tokensExpireIn(CarbonInterval::days(days: 15));
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(6));

        Response::macro('success', function ($data = [], $message = 'success', $code = 200) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ], $code);
        });
    }
}
