<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 02.03.18
 * Time: 12:24.
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
