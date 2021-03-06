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

namespace App\Utility;

use App\Notification\System\AbstractNotificationSystem;

class DiscordBotMessages
{
    const SIGNUP = [
        '{USER_MENTION} signed up for {EVENT_NAME}. We\'d better bring some more healers! [{CLASS} {ROLE}]',
        '{USER_MENTION} signed up for {EVENT_NAME}. Everbody hide your loot! [{CLASS} {ROLE}]',
        'Behold the wrath of Lorkhaj! {USER_MENTION} signed up for {EVENT_NAME}. [{CLASS} {ROLE}]',
        'Doom stands upon your treshold, {USER_MENTION} signed up for {EVENT_NAME}. [{CLASS} {ROLE}]',
        'The master draws his sword, {USER_MENTION} signed up for {EVENT_NAME}. [{CLASS} {ROLE}]',
        'Death by a hundred cuts, {USER_MENTION} signed up for {EVENT_NAME}. [{CLASS} {ROLE}]',
        'None shall be spared, {USER_MENTION} signed up for {EVENT_NAME}. [{CLASS} {ROLE}]',
        'There is no justice here, only judgement! {USER_MENTION} signed up for {EVENT_NAME}. [{CLASS} {ROLE}]',
    ];
    const EDIT              = '{USER_MENTION}, you were already signed up for {EVENT_NAME}. So I have updated your signup instead. [{CLASS} {ROLE}]';
    const SIGNOFF           = '{USER_MENTION}, you signed off for {EVENT_NAME}.';
    const SIGNED_UP_STATUS  = '{USER_MENTION}, you are signed up for {EVENT_NAME} as a {CLASS} {ROLE}.';
    const NOT_SIGNUP_STATUS = '{USER_MENTION}, you are not signed up for {EVENT_NAME}.';
    const HELP              = '{USER_MENTION}, here are the commands that you can use:'.PHP_EOL.
    '```'.PHP_EOL.
    '!events => List all upcoming events.'.PHP_EOL.
    '!signup [event_id] [class] [role] => Sign up for an event (use the event id from !events)'.PHP_EOL.
    '!signup [event_id] "[character preset name]" => Use this to sign up using one of your raidplanner character presets. Make sure the name is inside double quotes!'.PHP_EOL.
    '!signoff [event_id] => Sign off for an event (use the event id from !events)'.PHP_EOL.
    '!status [event_id] => Shows if you are signed up or not, and with what.'.PHP_EOL.
    '```'.PHP_EOL.
    '**Classes:** dragonknight (D), sorcerer (S), nightblade (N), warden (W), templar (T)'.PHP_EOL.
    '**Roles:** tank (T), healer (H), magickadd (M), staminadd (S), other (O)'.PHP_EOL.PHP_EOL.
    'A typical signup request would look like this `!signup 123 dragonknight tank` or the shorter version for lazy people `!signup 123 D T`';
    const HELP_EMBEDS = [
        'username'   => 'ESO Raidplanner',
        'content'    => '',
        'avatar_url' => 'https://esoraidplanner.com'.AbstractNotificationSystem::AVATAR_URL,
        'embeds'     => [[
            'title'       => 'ESO Raidplanner Commands',
            'description' => 'These are the commands you can use with the ESO Raidplanner bot.',
            'url'         => 'https://esoraidplanner.com/',
            'color'       => 9660137,
            'author'      => [
                'name'     => 'ESO Raidplanner',
                'url'      => 'https://esoraidplanner.com',
                'icon_url' => 'https://esoraidplanner.com/favicon/appicon.jpg',
            ],
            'fields' => [
                [
                    'name'   => '!events',
                    'value'  => 'List all upcoming events.',
                    'inline' => false,
                ],
                [
                    'name'   => '!signup [event_id] [class] [role]',
                    'value'  => 'Sign up for an event (use the event id from !events).',
                    'inline' => false,
                ],
                [
                    'name'   => '!signup "[character preset name]"',
                    'value'  => 'Use this to sign up using one of your raidplanner character presets. Make sure the name is inside double quotes!',
                    'inline' => false,
                ],
                [
                    'name'   => '!signoff [event_id]',
                    'value'  => 'Sign off for an event (use the event id from !events).',
                    'inline' => false,
                ],
                [
                    'name'   => '!status [event_id]',
                    'value'  => 'Shows if you are signed up or not, and with what.',
                    'inline' => false,
                ],
                [
                    'name'   => '!signups [event_id]',
                    'value'  => 'Shows the signup roster for an event.',
                    'inline' => false,
                ],
            ],
            'footer' => [
                'text'     => 'ESO Raidplanner by Woeler',
                'icon_url' => 'https://esoraidplanner.com/favicon/appicon.jpg',
            ],
        ]],
    ];

    public static function makeMention(string $uid)
    {
        return '<@'.$uid.'>';
    }
}
