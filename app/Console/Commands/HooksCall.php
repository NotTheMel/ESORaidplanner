<?php

/**
 * This file is part of the ESO Raidplanner project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/Woeler/eso-raid-planner
 */

namespace App\Console\Commands;

use App\Event;
use App\Guild;
use App\Hook;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HooksCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hooks:call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calls all user defined webhooks if they are ready to be called';

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
        $guilds = Guild::query()->get();

        foreach ($guilds as $guild) {
            $events = Event::query()->where('guild_id', '=', $guild->id)->where('start_date', '>=', date('Y-m-d H:i:s'))->get();

            $hooks = Hook::query()->where('guild_id', '=', $guild->id)->where('call_type', '=', 2)->get();

            foreach ($events as $event) {
                foreach ($hooks as $hook) {
                    $count = DB::table('hookcalls')->where('event_id', '=', $event->id)->where('hook_id', '=', $hook->id)->count();

                    if (0 === $count) {
                        $start_time = new DateTime($event->start_date);
                        $now        = new DateTime();

                        if ($now->getTimestamp() > ($start_time->getTimestamp() - $hook->call_time_diff)) {
                            if (0 === $hook->if_less_signups || empty($hook->if_less_signups)) {
                                $hook->call($event);
                            } elseif ($hook->if_less_signups > $event->getTotalSignups()) {
                                $hook->call($event);
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}
