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
 * @see https://github.com/ESORaidplanner/ESORaidplanner
 */

namespace App\Console\Commands;

use App\User;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Patreon\API;
use Patreon\OAuth;

class RenewPatreon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renew:patreon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renews access tokens for Patrons';

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
        $compare = new DateTime();

        $compare->setTimestamp(strtotime('-10 days'));

        $patrons = DB::table('user_patreon')->where('updated_at', '<', $compare->format('Y-m-d H:i:s'))->get();

        foreach ($patrons as $patron) {
            $oauth_client = new OAuth(env('PATREON_ID'), env('PATREON_SECRET'));

            $tokens = $oauth_client->refresh_token($patron->refresh_token, null);

            if (!empty($tokens['access_token'])) {
                DB::table('user_patreon')
                ->where('user_id', '=', $patron->user_id)
                ->update([
                    'access_token' => $tokens['access_token'],
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
            }

            $api_client      = new API($tokens['access_token']);
            $patron_response = $api_client->fetch_user();

            try {
                $patron_1 = $patron_response->get('data');
            } catch (\Exception $e) {
                continue;
            }

            $pledge = null;

            if ($patron_1->has('relationships.pledges')) {
                try {
                    $pledge = $patron_1->relationship('pledges')->get(0)->resolve($patron_response);
                } catch (\Exception $e) {
                    continue;
                }

                $cents = (int) $pledge->attribute('amount_cents');

                $u = User::query()->find($patron->user_id);

                if ($cents >= 100 && $cents < 500) {
                    $u->membership_level = 1;
                } elseif ($cents >= 500 && $cents < 1000) {
                    $u->membership_level = 2;
                } elseif ($cents >= 1000 && $cents < 1500) {
                    $u->membership_level = 3;
                } elseif ($cents >= 1500) {
                    $u->membership_level = 4;
                }

                $u->save();
            } else {
                DB::table('user_patreon')->where('id', '=', $patron->id)->delete();
            }
        }

        return true;
    }
}
