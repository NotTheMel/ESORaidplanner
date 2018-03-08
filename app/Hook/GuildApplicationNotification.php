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

namespace App\Hook;

use App\Guild;
use App\User;

class GuildApplicationNotification extends NotificationHook
{
    public function call(User $applicant)
    {
        $this->send($this->buildMessage($applicant));
    }

    private function buildMessage(User $applicant)
    {
        $guild = Guild::query()->find($this->guild_id);

        $message = str_replace(['{GUILD_NAME}', '{APPLICANT_NAME}', '{GUILD_URL}'], [$guild->name, $applicant->name, env('APP_URL').'/g/'.$guild->slug], $this->message);

        return $message;
    }
}
