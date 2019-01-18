<?php

namespace App\Console\Commands;

use App\Notification\Notification;
use Illuminate\Console\Command;

class CleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:all';

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
        foreach (Notification::all() as $notification) {
            $tags = [];
            foreach ($notification->tags() as $tag) {
                $tag = str_replace('\\', '', $tag);
                $tag = str_replace('"', '', $tag);
                $tag = str_replace(']', '', $tag);
                $tag = str_replace('[', '', $tag);

                if (!empty($tag)) {
                    $tags[] = $tag;
                }
            }
            $notification->tags = json_encode($tags);
            $notification->save();
        }
    }
}
