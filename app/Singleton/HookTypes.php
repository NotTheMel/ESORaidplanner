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

namespace App\Singleton;

class HookTypes
{
    const ON_TIME                   = 2;
    const ON_EVENT_CREATE           = 1;
    const ON_GUIDMEMBER_APPLICATION = 3;

    const ON_TIME_DESCRIPTION                    = 'Event start reminder notification';
    const ON_EVENT_CREATE_DESCRIPTION            = 'Event creation notification';
    const ON_GUILDMEMBER_APPLICATION_DESCRIPTION = 'Guild application notification';

    public static function getTypeDescription(int $type): string
    {
        switch ($type) {
            case self::ON_TIME:
               $description =  'Event start reminder notification';
               break;
            case self::ON_EVENT_CREATE:
                $description = 'Event creation notification';
                break;
            case self::ON_GUIDMEMBER_APPLICATION:
                $description = 'Guild application notification';
                break;
            default:
                $description = '';
        }

        return $description;
    }
}
