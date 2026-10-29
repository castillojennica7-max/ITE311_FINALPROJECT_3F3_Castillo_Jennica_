<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\StrapiService;
use Illuminate\Console\Command;

class SyncStrapi extends Command
{
    protected $signature = 'strapi:sync';

    protected $description = 'Sync all users and the daily report to the Strapi CMS';

    public function handle(StrapiService $strapi): int
    {
        $ok = 0;
        $failed = 0;

        foreach (User::all() as $user) {
            $strapi->syncUser($user) ? $ok++ : $failed++;
        }

        $this->info("Users synced: $ok, failed: $failed");

        $strapi->syncDailyReport()
            ? $this->info('Daily report synced.')
            : $this->warn('Daily report failed. Check storage/logs/laravel.log');

        return self::SUCCESS;
    }
}