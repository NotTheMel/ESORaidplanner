<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 11.10.18
 * Time: 11:59.
 */

namespace App\Utility;

class Races
{
    const ALTMER   = 1;
    const BOSMER   = 2;
    const KHAJIIT  = 3;
    const NORD     = 4;
    const ORC      = 5;
    const BRETON   = 6;
    const REDGUARD = 7;
    const DUNMER   = 8;
    const ARGONIAN = 9;
    const IMPERIAL = 10;

    const RACES = [
        self::ALTMER   => 'Altmer',
        self::BOSMER   => 'Bosmer',
        self::KHAJIIT  => 'Khajiit',
        self::NORD     => 'Nord',
        self::ORC      => 'Orc',
        self::BRETON   => 'Breton',
        self::REDGUARD => 'Redguard',
        self::DUNMER   => 'Dunmer',
        self::ARGONIAN => 'Argonian',
        self::IMPERIAL => 'Imperial',
    ];

    public static function getImage(int $race): string
    {
        switch ($race) {
            case self::ALTMER:
                return 'race_altmer.png';

            case self::BOSMER:
                return 'race_bosmer.png';

            case self::KHAJIIT:
                return 'race_khajiit.png';

            case self::NORD:
                return 'race_nord.png';

            case self::ORC:
                return 'race_orc.png';

            case self::BRETON:
                return 'race_breton.png';

            case self::REDGUARD:
                return 'race_redguard.png';

            case self::DUNMER:
                return 'race_dunmer.png';

            case self::ARGONIAN:
                return 'race_argonian.png';

            case self::IMPERIAL:
                return 'race_imperial.png';

            default:
                return '';
        }
    }
}
