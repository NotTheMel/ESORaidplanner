<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 13.10.18
 * Time: 15:49.
 */

namespace App\Notification\System;

use App\Notification\Notification;

class TelegramSystem extends AbstractNotificationSystem
{
    const SYSTEM_ID = 2;
    const NAME      = 'Telegram';

    public function send(Notification $notification)
    {
        $params = [
            'chat_id' => $notification->chat_id,
            'text'    => $this->message,
        ];

        $ch = curl_init('https://api.telegram.org/bot'.$notification->token.'/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_exec($ch);
        curl_close($ch);
    }
}
