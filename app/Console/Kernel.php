<?php

namespace App\Console;

use App\Console\Commands\ApproveAffiliateCommissions;
use App\Console\Commands\CreateGames;
use App\Console\Commands\DeleteEmptyGameRooms;
use App\Console\Commands\DeleteUnusedProvablyFairGames;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    protected $scheduledCommands = [
        DeleteEmptyGameRooms::class => 'daily',
        DeleteUnusedProvablyFairGames::class => 'daily'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Bots
        $frequencyMethod = config('settings.bots.games.frequency');
        $schedule->command(CreateGames::class)->$frequencyMethod();

        // affiliate commissions
        if (config('settings.affiliate.auto_approval_frequency') == 'daily') {
            $schedule->command(ApproveAffiliateCommissions::class)->dailyAt('00:00');
        } elseif (config('settings.affiliate.auto_approval_frequency') == 'weekly') {
            $schedule->command(ApproveAffiliateCommissions::class)->weeklyOn(1, '00:00');
        } elseif (config('settings.affiliate.auto_approval_frequency') == 'monthly') {
            $schedule->command(ApproveAffiliateCommissions::class)->monthlyOn(1, '00:00');
        }

        collect($this->scheduledCommands)->each(function ($frequency, $command) use ($schedule) {
            $schedule->command($command)->$frequency();
        });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
