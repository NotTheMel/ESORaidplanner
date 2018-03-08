<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 07.03.18
 * Time: 08:59.
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

        $message = str_replace('{GUILD_NAME}', $guild->name, $this->message);
        $message = str_replace('{APPLICANT_NAME}', $applicant->name, $message);
        $message = str_replace('{GUILD_URL}', 'https://esoraidplanner.com/g/'.$guild->slug, $message);

        return $message;
    }
}
