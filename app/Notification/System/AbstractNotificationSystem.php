<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 13.10.18
 * Time: 15:28.
 */

namespace App\Notification\System;

use App\Notification\Message\AbstractNotificationMessage;
use App\Notification\Notification;

abstract class AbstractNotificationSystem
{
    const AVATAR_URL = '/storage/assets/app_icon.jpg';
    const SYSTEM_ID  = 0;
    const NAME       = '';
    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $embeds;

    /**
     * @var AbstractNotificationMessage
     */
    protected $messageObj;

    public function __construct(AbstractNotificationMessage $message)
    {
        $this->message    = $message->getText();
        $this->embeds     = $message->getEmbeds();
        $this->messageObj = $message;
    }

    abstract public function send(Notification $notification);
}
