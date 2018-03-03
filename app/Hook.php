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

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Hook extends Model
{
    const TYPE_DISCORD  = 1;
    const TYPE_TELEGRAM = 2;
    const TYPE_SLACK    = 3;
    const AVATAR_URL = '/storage/assets/app_icon.jpg';

    /**
     * @param Event $event
     */
    public function call(Event $event)
    {
        $message = $this->makeMessage($this->message, $event);

        // DISCORD
        if (self::TYPE_DISCORD === $this->type) {
            $ch = curl_init($this->url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(
                [
                    'content' => $message,
                    'username' => 'ESO Raidplanner',
                    'avatar' => env('APP_URL').self::AVATAR_URL,
                ]
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            if (false !== $result) {
                $this->wasCalled($event);
            }

            // TELEGRAM
        } elseif (self::TYPE_TELEGRAM === $this->type) {
            $params = [
                'chat_id' => $this->chat_id,
                'text'    => $message,
            ];

            $ch = curl_init('https://api.telegram.org/bot'.$this->token.'/sendMessage');
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
            $result = curl_exec($ch);
            curl_close($ch);

            if (false !== $result) {
                $this->wasCalled($event);
            }

            // SLACK
        } elseif (self::TYPE_SLACK === $this->type) {
            $params = 'payload='.json_encode(['text' => $message]);

            $ch = curl_init($this->url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            if (false !== $result) {
                $this->wasCalled($event);
            }
        }
    }

    /**
     * @param Event $event
     */
    private function wasCalled(Event $event)
    {
        Hookcall::query()->insert([
            'hook_id'    => $this->id,
            'event_id'   => $event->id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
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
     * @param string $message
     * @param Event  $event
     *
     * @return string
     */
    private function makeMessage(string $message, Event $event): string
    {
//        $date = new DateTime($event->start_date);

        $guild = $event->getGuild();

        $message = str_replace('{EVENT_NAME}', $event->name, $message);
        $message = str_replace('{EVENT_DESCRIPTION}', $event->description, $message);
        $message = str_replace('{EVENT_NUM_SIGNUPS}', $event->getTotalSignups(), $message);
        $message = str_replace('{EVENT_URL}', 'https://esoraidplanner.com/g/'.$guild->slug.'/event/'.$event->id, $message);

        return $message;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function isOwner(int $id): bool
    {
        if (!empty($this->guild_id)) {
            $guild = Guild::query()->find($this->guild_id);

            if (in_array($id, json_decode($guild->admins, true))) {
                return true;
            }
        } elseif (!empty($this->user_id)) {
            if ($this->user_id === $id) {
                return true;
            }
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
}
