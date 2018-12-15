<?php

namespace App\Console\Commands;

use App\Guild;
use Illuminate\Console\Command;

class CleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instance:clean';

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
        $progress = 0;

        foreach (Guild::query()->where('active', '=', 1)->get()->all() as $guild) {
            $lastEvent = $guild->events()->orderBy('start_date', 'desc')->first();

            $limit = new \DateTime('@'.strtotime('-6 months'), new \DateTimeZone(env('DEFAULT_TIMEZONE')));

            if (null === $lastEvent) {
                $last = new \DateTime($guild->updated_at ?? $guild->created_at, new \DateTimeZone(env('DEFAULT_TIMEZONE')));
            } else {
                $last = new \DateTime($lastEvent->start_date, new \DateTimeZone(env('DEFAULT_TIMEZONE')));
            }

            if ($last < $limit) {
                $this->info('Guild deactivated: '.$guild->name);
                $guild->active = 0;
                $guild->save();
                ++$progress;
            }
        }

        $this->info('Deactivated '.$progress.' inactive guilds.');
    }
}
