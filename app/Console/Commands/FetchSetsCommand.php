<?php

namespace App\Console\Commands;

use App\Set;
use Illuminate\Console\Command;

class FetchSetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:sets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://eso-sets.com/api/esoraidplanner");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        print_r($output);

        $version = Set::query()->orderBy('version', 'desc')->first()->version;

        foreach (json_decode($output, true) as $key => $name)
        {
            $db = Set::query()->find($key);

            if (empty($db)){
                $set = new Set();
                $set->id = $key;
                $set->name = $name;
                $set->version = $version + 1;
                $set->save();
            } else {
                $db->name = $name;
                $db->save();
            }
        }
    }
}
