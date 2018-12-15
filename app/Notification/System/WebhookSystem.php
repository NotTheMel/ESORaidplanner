<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 15.10.18
 * Time: 09:15.
 */

namespace App\Notification\System;

use App\Notification\Notification;

class WebhookSystem extends AbstractNotificationSystem
{
    const SYSTEM_ID = 4;
    const NAME      = 'Custom Webhook';

    public function send(Notification $notification)
    {
        $data = [
            'subjects' => $this->messageObj->getSubjects(),
            'text'     => $this->message,
            'type'     => $this->messageObj::IDEN,
            'date'     => date('Y-m-d H:i:s'),
        ];

        $ch = curl_init($notification->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
