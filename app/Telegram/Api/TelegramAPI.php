<?php

namespace App\Telegram\Api;

use App\Character;
use Illuminate\Support\Facades\DB;

class TelegramAPI
{
    /** Properties retrieved from telegram user**/
    public static $chatId;
    public static $chatType;
    public static $username;
    public static $message;

    /**
     * @param $updates
     */
    public static function load($updates)
    {
        if (empty($updates['message'])
            || $updates['message']['chat']['type'] !== 'private'
            || empty($updates['message']['chat']['username'])
            || empty($updates['message']['chat']['id'])
            || empty($updates['message']['text'])
        ) {
            exit;
        }
        self::$chatId   = $updates['message']['chat']['id'];
        self::$chatType = $updates['message']['chat']['type'];
        self::$username = $updates['message']['chat']['username'];
        self::$message  = $updates['message']['text'];
    }

    public static function addUser()
    {
        DB::table('telegram')->insert(['username' => self::$username]);
    }

    /**
     * @return int
     */
    public static function getMenuId(): int
    {
        $telegram = DB::table('telegram')->where('username', '=', self::$username)->first();

        if (!empty($telegram)) {
            return $telegram->menu_id ?? 0;
        }

        return 0;
    }

    /**
     * @param $menuId
     */
    public static function setMenuId(int $menuId)
    {
        DB::table('telegram')->where('username', '=', self::$username)->update(['menu_id' => $menuId]);
    }

    /**
     * @return int
     */
    public static function getGuildId(): int
    {
        $telegram = DB::table('telegram')->where('username', '=', self::$username)->first();

        if (!empty($telegram)) {
            return $telegram->guild_id ?? 0;
        }

        return 0;
    }

    /**
     * @param int $guildId
     */
    public static function setGuildId(int $guildId)
    {
        DB::table('telegram')->where('username', '=', self::$username)->update(['guild_id' => $guildId]);
    }

    /**
     * @return int
     */
    public static function getEventId(): int
    {
        $telegram = DB::table('telegram')->where('username', '=', self::$username)->first();

        if (!empty($telegram)) {
            return $telegram->event_id ?? 0;
        }

        return 0;
    }

    /**
     * @param int $eventId
     */
    public static function setEventId(int $eventId)
    {
        DB::table('telegram')->where('username', '=', self::$username)->update(['event_id' => $eventId]);
    }

    /**
     * @return Character|null
     */
    public static function getPreset()
    {
        $telegram = DB::table('telegram')->where('username', '=', self::$username)->first();

        if (!empty($telegram)) {
            $preset              = new Character();
            $preset->name        = 'Unnamed preset';
            $preset->role        = $telegram->role;
            $preset->class       = $telegram->class;
            $preset->supportSets = $telegram->support_sets;

            return $preset;
        }

        return null;
    }

    /**
     * @param int $role
     */
    public static function setRole(int $role)
    {
        DB::table('telegram')->where('username', '=', self::$username)->update(['role' => $role]);
    }

    /**
     * @param int $class
     */
    public static function setClass(int $class)
    {
        DB::table('telegram')->where('username', '=', self::$username)->update(['class' => $class]);
    }

    /**
     * @param string $supportSets
     */
    public static function setSupportSets(string $supportSets = null)
    {
        DB::table('telegram')->where('username', '=', self::$username)->update(['support_sets' => $supportSets ?? '']);
    }

    public static function getComments()
    {
        $telegram = DB::table('telegram')->where('username', '=', self::$username)->first();

        if (!empty($telegram)) {
            return $telegram->comment;
        }

        return null;
    }

    /**
     * @param string $comments
     */
    public static function setComments(string $comments = null)
    {
        DB::table('telegram')->where('username', '=', self::$username)->update(['comment' => $comments ?? '']);
    }

    /**
     * @param int $role
     *
     * @return string
     */
    public static function getShortRoleText(int $role): string
    {
        switch ($role) {
            case 1:
                return 'Tank';

            case 2:
                return 'Healer';

            case 4:
                return 'Stamina DD';

            case 3:
                return 'Magicka DD';

            default:
                return '';
        }
    }

    /**
     * @param int $role
     *
     * @return string
     */
    public static function getLargeRoleText(int $role): string
    {
        switch ($role) {
            case 'Tank':
                return 1;

            case 'Healer':
                return 2;

            case 'Stamina DD':
                return 4;

            case 'Magicka DD':
                return 3;

            default:
                return '';
        }
    }

    /**
     * @param int $role
     *
     * @return string
     */
    public static function getRoleIcon(int $role): string
    {
        switch ($role) {
            case 1:
                return '🔰';

            case 2:
                return '⛑';

            case 4:
                return '⚔';

            case 3:
                return '🔮';

            default:
                return '';
        }
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getMegaserverName(int $id): string
    {
        switch ($id) {
            case 1:
                return 'EU';

            case 2:
                return 'NA';

            default:
                return 'EU';
        }
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getPlatformName(int $id): string
    {
        switch ($id) {
            case 1:
                return 'PC';

            case 2:
                return 'PS4';

            default:
                return 'XBOX';
        }
    }

    /**
     * @param string $class
     *
     * @return int
     */
    public static function getClassId(string $class): int
    {
        switch ($class) {
            case 'Dragonknight':
                return 1;

            case 'Sorcerer':
                return 2;

            case 'Nightblade':
                return 3;

            case 'Warden':
                return 4;

            case 'Templar':
                return 6;

            default:
                return '';
        }
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getClassName(int $id): string
    {
        switch ($id) {
            case 1:
                return 'Dragonknight';

            case 2:
                return 'Sorcerer';

            case 3:
                return 'Nightblade';

            case 4:
                return 'Warden';

            case 6:
                return 'Templar';

            default:
                return '';
        }
    }

    /**
     * @param $message
     * @param $keyboard
     */
    public static function reply($message, $keyboard)
    {
        $bot_website = 'https://api.telegram.org/bot'.env('TELEGRAM_BOT_TOKEN');

        for ($index = 0; $index < count($message) - 1; ++$index) {
            file_get_contents($bot_website.'/sendmessage?chat_id='.self::$chatId.'&text='.urlencode($message[$index]));
        }

        if (!empty($keyboard)) {
            $keyboard_markup = json_encode(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

            file_get_contents($bot_website.'/sendmessage?chat_id='.self::$chatId.'&text='.urlencode($message[$index]).'&reply_markup='.urlencode($keyboard_markup));
        }
    }
}
