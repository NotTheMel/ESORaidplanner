<?php

namespace App\Console\Commands;

use App\Event;
use App\RepeatableEvent;
use Illuminate\Console\Command;
use Woeler\DiscordPhp\Exception\DiscordInvalidResponseException;

class CreateRepeatableEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repeatables:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $intervalMap = [
        'DAILY'   => 'days',
        'WEEKLY'  => 'weeks',
        'MONTHLY' => 'months',
    ];

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
        /*
         * The following code creates recurring events
         * It works with DST in every timezone
         * Please be careful changing this
         */
        foreach (RepeatableEvent::all() as $repeatable) {
            $timezone  = $repeatable->timezone;
            $startDate = new \DateTime($repeatable->latest_event_date, new \DateTimeZone(env('DEFAULT_TIMEZONE')));
            $startDate->setTimezone(new \DateTimeZone($timezone));
            $rule = new \Recurr\Rule('FREQ='.$repeatable->interval.';COUNT='.($repeatable->max_create_ahead + 1), $startDate, null, $timezone);

            $transformer = new \Recurr\Transformer\ArrayTransformer();
            $dates       = $transformer->transform($rule);

            $gate_time = new \DateTime('@'.strtotime('+'.($repeatable->max_create_ahead + 1).' '.$this->intervalMap[$repeatable->interval]), new \DateTimeZone(env('DEFAULT_TIMEZONE')));

            foreach ($dates as $date) {
                $start = $date->getStart();
                $start->setTimezone(new \DateTimeZone(env('DEFAULT_TIMEZONE')));

                if ($gate_time->getTimestamp() < $start->getTimestamp()) {
                    continue;
                }

                $event = Event::query()
                    ->where('start_date', '=', $start->format('Y-m-d H:i:s'))
                    ->where('parent_repeatable', '=', $repeatable->id)
                    ->first();

                if (null === $event) {
                    $new_event                    = new Event();
                    $new_event->name              = $repeatable->name;
                    $new_event->description       = $repeatable->description;
                    $new_event->start_date        = $start->format('Y-m-d H:i:s');
                    $new_event->guild_id          = $repeatable->guild_id;
                    $new_event->parent_repeatable = $repeatable->id;
                    $new_event->tags              = $repeatable->tags ?? [];
                    try {
                        $new_event->sendCreationNotifications();
                    } catch (DiscordInvalidResponseException $e) {
                        if (404 !== $e->getCode()) {
                            break;
                        }
                    }
                    $new_event->save();
                    $start->setTimezone(new \DateTimeZone(env('DEFAULT_TIMEZONE')));
                    $repeatable->latest_event      = $new_event->id;
                    $repeatable->latest_event_date = $start->format('Y-m-d H:i:s');
                    $repeatable->save();
                    $this->line('Event created: '.$new_event->name);
                }
            }
        }
    }
}
