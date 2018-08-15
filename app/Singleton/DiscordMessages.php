<?php
/**
 * This file is part of the ESO-Database project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://eso-database.com
 * Created by woeler
 * Date: 15.08.18
 * Time: 19:30
 */

namespace App\Singleton;

class DiscordMessages
{
    const SIGNUP = [
        '{USER_MENTION} signed up for {EVENT_NAME}. We\'d better bring some more healers!',
        '{USER_MENTION} signed up for {EVENT_NAME}. Everbody hide your loot!',
        'Behold the wrath of Lorkhaj! {USER_MENTION} signed up for {EVENT_NAME}.',
        'Doom stands upon your treshold, {USER_MENTION} signed up for {EVENT_NAME}.',
        'The master draws his sword, {USER_MENTION} signed up for {EVENT_NAME}.',
        'Death by a hundred cuts, {USER_MENTION} signed up for {EVENT_NAME}.',
        'None shall be spared, {USER_MENTION} signed up for {EVENT_NAME}.',
        'There is no justice here, only judgement! {USER_MENTION} signed up for {EVENT_NAME}.',
    ];

    const HELP = '{USER_MENTION}, here are the commands that you can use:'.PHP_EOL.
    '`!events` => List all upcoming events.'.PHP_EOL.
    '`!signup [event_id] [class] [role]` => Sign up for an event (use the event id from `!events`)'.PHP_EOL.
    '`!signoff [event_id]` => Sign off for an event (use the event id from `!events`)'.PHP_EOL.
    '**Classes:** `dragonknight`, `sorcerer`, `nightblade`, `warden`, `templar`'.PHP_EOL.
    '**Roles:** `tank`, `healer`, `magickadd`, `staminadd`, `other`'.PHP_EOL.PHP_EOL.
    'A typical signup request would look like this `!signup 123 dragonknight tank`';
}
