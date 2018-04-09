<?php

namespace App\Console\Commands;

use App\Event;
use App\LogEntry;
use App\RepeatableEvent;
use DateTime;
use Illuminate\Console\Command;

class RepeatableEventsCommand extends Command
{
    const FOUR_WEEKS = 2419200;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:events';

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
        $repeatables = RepeatableEvent::query()->get()->all() ?? [];

        foreach ($repeatables as $repeatable) {
            $last_event = new DateTime($repeatable->latest_event);
            $next_event = $last_event->modify('+'.$repeatable->interval.' seconds');
            $now        = new DateTime();
            $now->modify('+'.$repeatable->create_interval.' seconds');

            if ($next_event < $now) {
                $event                    =  new Event();
                $event->name              = $repeatable->name;
                $event->description       = $repeatable->description;
                $event->type              = $repeatable->type;
                $event->start_date        = $next_event->format('Y-m-d H:i:s');
                $event->guild_id          = $repeatable->guild_id;
                $event->parent_repeatable = $repeatable->id;
                $event->save();

                $event->callEventCreationHooks();

                $repeatable->latest_event = $event->start_date;
                $repeatable->save();

                $log = new LogEntry();
                $log->create($repeatable->guild_id, 'System automatically created the event '.$event->name.'.');
            }
        }
    }
}
