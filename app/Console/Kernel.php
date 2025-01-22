<?php

namespace App\Console;

use Carbon\Carbon;
use App\Models\Investments;
use App\Jobs\EarningCalculator;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            // Call your service to handle the task assignment
            $this->getAllInvestmentTransactions();
        })->everyMinute();
        $schedule->command('queue:work --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    public function getAllInvestmentTransactions()
    {
        $model  = Investments::where('status', 0)
            ->where(function ($query) {
                $query->where('last_run', '<', Carbon::now()->toDateString())
                    ->orWhereNull('last_run');
            })
            ->limit(20)
            ->get();

        EarningCalculator::dispatch($model->toArray());
        Log::info('EarningCalculator job dispatched.');
        // Update the `last_run` column for the fetched records
        $model->each(function ($record) {
            $record->last_run = Carbon::now(); // Set `last_run` to the current date
            $record->save();                   // Save the updated record
        });
    }
}
