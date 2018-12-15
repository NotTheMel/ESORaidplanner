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
        curl_setopt($ch, CURLOPT_URL, 'https://eso-sets.com/api/esoraidplanner');
        $headers = [
            'Content-Type:application/json',
            'Authorization: Basic '.base64_encode(env('ESOSETS_API_KEY')), ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $version = Set::query()->orderBy('version', 'desc')->first()->version;
        foreach (json_decode($output, true) as $key => $data) {
            $db = Set::query()->find($key);
            if (empty($db)) {
                $set          = new Set();
                $set->id      = $key;
                $set->name    = $data['name'];
                $set->tooltip = str_replace('"', "'", $data['tooltip']);
                $set->version = $version + 1;
                $set->save();
            } else {
                $db->name    = $data['name'];
                $db->tooltip = str_replace('"', "'", $data['tooltip']);
                $db->save();
            }
        }
    }
}
