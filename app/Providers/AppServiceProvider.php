<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Appointment;
use App\Models\User;
use App\Services\StrapiService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
        public function boot(): void
    {
        // Kapag may bagong user o nabago ang profile, ipapadala sa CMS
        User::saved(function (User $user) {
            app(StrapiService::class)->syncUser($user);
        });

        // Kapag may bagong booking o nagbago ang status, i-update ang report sa CMS
        Appointment::saved(function () {
            app(StrapiService::class)->syncDailyReport();
        });
    }
}
