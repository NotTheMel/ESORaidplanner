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

namespace App\Hook;

use App\Event;
use App\Guild;
use App\Hookcall;
use App\User;
use Illuminate\Database\Eloquent\Model;

class NotificationHook extends Model
{
    const TYPE_DISCORD  = 1;
    const TYPE_TELEGRAM = 2;
    const TYPE_SLACK    = 3;
    const AVATAR_URL    = '/storage/assets/app_icon.jpg';

    protected $table = 'hooks';

    protected $fillable = [
        'name', 'type', 'token', 'chat_id', 'url', 'call_time_diff', 'active', 'guild_id', 'if_less_signups', 'call_type', 'tags', 'message',
    ];

    protected function discord(string $message)
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(
            [
                'content'    => $message,
                'username'   => 'ESO Raidplanner',
                'avatar_url' => env('APP_URL').self::AVATAR_URL,
            ]
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    protected function telegram(string $message)
    {
        $params = [
            'chat_id' => $this->chat_id,
            'text'    => $message,
        ];

        $ch = curl_init('https://api.telegram.org/bot'.$this->token.'/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_exec($ch);
        curl_close($ch);
    }

    protected function slack(string $message)
    {
        $params = 'payload='.json_encode(['text' => $message]);

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    protected function wasCalled(Event $event)
    {
        Hookcall::query()->insert([
            'hook_id'    => $this->id,
            'event_id'   => $event->id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function send(string $message)
    {
        if (self::TYPE_DISCORD === $this->type) {
            $this->discord($message);
        } elseif (self::TYPE_TELEGRAM === $this->type) {
            $this->telegram($message);
        } elseif (self::TYPE_SLACK === $this->type) {
            $this->slack($message);
        }
    }

    /**
     * @return string
     */
    public function getHookType(): string
    {
        if (1 === $this->type) {
            return 'Discord';
        } elseif (2 === $this->type) {
            return 'Telegram';
        } elseif (3 === $this->type) {
            return 'Slack';
        } else {
            return 'Unknown';
        }
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function isOwner(User $user): bool
    {
        $guild = Guild::query()->find($this->guild_id);

        if (in_array($user->id, json_decode($guild->admins, true))) {
            return true;
        }

        return false;
    }

    /**
     * @param Event $event
     *
     * @return bool
     */
    public function matchesEventTags(Event $event): bool
    {
        if (empty($this->tags)) {
            return true;
        }

        if (empty($event->tags)) {
            return false;
        }

        if (false !== strpos($event->tags, ',')) {
            $event_tags = explode(',', $event->tags);
        } else {
            $event_tags = [$event->tags];
        }
        if (false !== strpos($this->tags, ',')) {
            $hook_tags = explode(',', $this->tags);
        } else {
            $hook_tags = [$this->tags];
        }

        foreach ($event_tags as $event_tag) {
            foreach ($hook_tags as $hook_tag) {
                if (trim($event_tag) === trim($hook_tag)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return Guild|null
     */
    public function getGuild(): ?Guild
    {
        if (null === $this->guild_id) {
            return null;
        }

        return Guild::query()->find($this->guild_id);
    }

    public static function getTypeName(int $type)
    {
        switch ($type) {
            case self::TYPE_DISCORD:
                return 'Discord';
                break;
            case self::TYPE_TELEGRAM:
                return 'Telegram';
                break;
            case self::TYPE_SLACK:
                return 'Slack';
                break;
            default:
                return '';
        }
    }
}
