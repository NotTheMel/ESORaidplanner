<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 13.10.18
 * Time: 15:49.
 */

namespace App\Notification\System;

use App\Notification\Notification;

class SlackSystem extends AbstractNotificationSystem
{
    const SYSTEM_ID = 3;
    const NAME      = 'Slack';

    public function send(Notification $notification)
    {
        $params = 'payload='.json_encode(['text' => $this->message]);
        $ch     = curl_init($notification->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
