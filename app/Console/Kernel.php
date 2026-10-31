<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{


    protected $commands = [
       Commands\DeletePendingTransactionsCron::class,
       Commands\RefundFailedTransactionVTPassCron::class,
        Commands\AirtelCron::class,
        Commands\RingoCron::class,
        Commands\JonetCron::class,
        Commands\AwufCron::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('deletependingtransaction:cron')->daily();

        $schedule->command('refundfailedtransactionvtpass:cron')->everyMinute();
        
        $schedule->command('airtel:cron')
                 ->everyMinute();
                 
       $schedule->command('ringo:cron')->everyMinute();
       $schedule->command('jonet:cron')->everyMinute();
        $schedule->command('awufswitcher:cron')->dailyAt('7:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
