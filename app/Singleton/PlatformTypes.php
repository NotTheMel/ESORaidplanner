<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 08.03.18
 * Time: 15:09.
 */

namespace App\Singleton;

class PlatformTypes
{
    const PLATFORM_PC_MAC   = 1;
    const PLATFORM_PS4      = 2;
    const PLATFORM_XBOX_ONE = 3;

    /**
     * @param int $platform_id
     *
     * @return string
     */
    public static function getPlatformName(int $platform_id): string
    {
        switch ($platform_id) {
            case self::PLATFORM_PC_MAC:
                return 'PC';

            case self::PLATFORM_PS4:
                return 'PS4';

            case self::PLATFORM_XBOX_ONE:
                return 'XBOX One';

            default:
                return 'Unknown';
        }
    }

    /**
     * @param int $platform_id
     *
     * @return string
     */
    public static function getPlatformShort(int $platform_id): string
    {
        if (self::PLATFORM_PC_MAC === $platform_id) {
            return 'PC';
        } elseif (self::PLATFORM_PS4 === $platform_id) {
            return 'PS4';
        } elseif (self::PLATFORM_XBOX_ONE === $platform_id) {
            return 'XBOX';
        }

        return 'Unknown';
    }
}
