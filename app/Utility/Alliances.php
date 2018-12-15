<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 11.10.18
 * Time: 11:52.
 */

namespace App\Utility;

class Alliances
{
    const ALDMERI_DOMINION    = 1;
    const EBONHEART_PACT      = 2;
    const DAGGERFALL_COVENANT = 3;

    const ALLIANCES = [
        self::ALDMERI_DOMINION    => 'Aldmeri Dominion',
        self::EBONHEART_PACT      => 'Ebonheart Pact',
        self::DAGGERFALL_COVENANT => 'Daggerfall Covenant',
    ];

    public static function getImage(int $class): string
    {
        switch ($class) {
            case self::ALDMERI_DOMINION:
                return 'ad.png';

            case self::EBONHEART_PACT:
                return 'ep.png';

            case self::DAGGERFALL_COVENANT:
                return 'dc.png';

            default:
                return '';
        }
    }
}
