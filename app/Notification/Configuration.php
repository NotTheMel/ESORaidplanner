<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 15.10.18
 * Time: 11:32.
 */

namespace App\Notification;

use App\Notification\Message\EventCreationMessage;
use App\Notification\Message\EventDeletionMessage;
use App\Notification\Message\GuildApplicationMessage;
use App\Notification\Message\PostSignupsMessage;
use App\Notification\Message\ReminderMessage;
use App\Notification\Message\UpcomingEventsMessage;
use App\Notification\System\DiscordSystem;
use App\Notification\System\SlackSystem;
use App\Notification\System\TelegramSystem;
use App\Notification\System\WebhookSystem;

class Configuration
{
    /**
     * Here all message types are registered so that
     * they pop up in the user form.
     */
    const MESSAGE_TYPES = [
        EventCreationMessage::CALL_TYPE    => EventCreationMessage::class,
        EventDeletionMessage::CALL_TYPE    => EventDeletionMessage::class,
        PostSignupsMessage::CALL_TYPE      => PostSignupsMessage::class,
        ReminderMessage::CALL_TYPE         => ReminderMessage::class,
        GuildApplicationMessage::CALL_TYPE => GuildApplicationMessage::class,
        UpcomingEventsMessage::CALL_TYPE   => UpcomingEventsMessage::class,
    ];

    /**
     * Here all the system types are registered so that
     * they pop up in the user form.
     */
    const SYSTEM_TYPES = [
        DiscordSystem::SYSTEM_ID  => DiscordSystem::class,
        SlackSystem::SYSTEM_ID    => SlackSystem::class,
        TelegramSystem::SYSTEM_ID => TelegramSystem::class,
        WebhookSystem::SYSTEM_ID  => WebhookSystem::class,
    ];

    /**
     * Messages that trigger on a time relative to X.
     */
    const RELATIVE_TIME_BASED_MESSAGES = [
        ReminderMessage::CALL_TYPE,
    ];

    /**
     * Messages that trigger daily at a specific time.
     */
    const DAILY_MESSAGES = [
        UpcomingEventsMessage::CALL_TYPE,
    ];

    /**
     * Timezone based messages.
     */
    const TIMEZONE_BASED_MESSAGES = [
        UpcomingEventsMessage::CALL_TYPE,
    ];

    /**
     * Messages that have customizable text.
     */
    const HAS_CUSTOM_TEXT_MESSAGE = [
        EventCreationMessage::CALL_TYPE,
        EventDeletionMessage::CALL_TYPE,
        PostSignupsMessage::CALL_TYPE,
        ReminderMessage::CALL_TYPE,
        GuildApplicationMessage::CALL_TYPE,
    ];

    /**
     * Messages that have the if more than or less than X signups options.
     */
    const SIGNUP_BASED_MESSAGES = [
        ReminderMessage::CALL_TYPE,
    ];

    /**
     * Messages that use tags.
     */
    const TAG_BASED_MESSAGES = [
        ReminderMessage::CALL_TYPE,
        EventDeletionMessage::CALL_TYPE,
        EventCreationMessage::CALL_TYPE,
    ];
}
