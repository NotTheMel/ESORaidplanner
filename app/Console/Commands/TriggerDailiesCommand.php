<?php

namespace App\Console\Commands;

use App\Guild;
use App\Notification\Notification;
use App\Utility\Cron;
use Illuminate\Console\Command;

class TriggerDailiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trigger:dailies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (Cron::isRunning($this->signature)) {
            return;
        }

        Cron::start($this->signature);

        $guilds = Guild::all();
        foreach ($guilds as $guild) {
            /** @var Notification $notification */
            foreach ($guild->dailyNotifications() as $notification) {
                $notification->send(['events' => $guild->upcomingEvents(), 'guild' => $guild]);
            }
        }

        Cron::finish($this->signature);
    }
}
