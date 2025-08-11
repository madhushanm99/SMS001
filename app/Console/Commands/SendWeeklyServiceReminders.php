<?php

namespace App\Console\Commands;

use App\Jobs\SendServiceReminders;
use Illuminate\Console\Command;

class SendWeeklyServiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly service reminder emails for vehicles due next week and follow-ups.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        SendServiceReminders::dispatch();
        $this->info('Service reminders dispatched to queue.');
        return self::SUCCESS;
    }
}


