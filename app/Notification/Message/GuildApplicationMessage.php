<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 13.10.18
 * Time: 15:59.
 */

namespace App\Notification\Message;

use App\Notification\System\AbstractNotificationSystem;
use Woeler\DiscordPhp\Message\DiscordEmbedsMessage;

class GuildApplicationMessage extends AbstractNotificationMessage
{
    const CALL_TYPE = 3;
    const IDEN      = 'guild.member.application';
    const CONFIG    = [
        'identifier'  => 'guild.member.application',
        'name'        => 'Guild application notification',
        'description' => 'Sends a notification every time requests membership of this guild.',
    ];

    public function hasNeededSubjects(): bool
    {
        return array_key_exists('guild', $this->subjects) && array_key_exists('user', $this->subjects);
    }

    protected function buildDiscordEmbeds(): DiscordEmbedsMessage
    {
        $embeds = new DiscordEmbedsMessage();
        $embeds->setUsername('ESO Raidplanner');
        $embeds->setAvatar('https://esoraidplanner.com'.AbstractNotificationSystem::AVATAR_URL);
        $embeds->setTitle('New member "'.$this->subjects['user']->name.'" applied to '.$this->subjects['guild']->name);
        $embeds->setUrl('https://esoraidplanner.com/g/'.$this->subjects['guild']->slug);
        $embeds->setDescription($this->getText());
        $embeds->setColor(9660137);
        $embeds->setAuthorName('ESO Raidplanner');
        $embeds->setAuthorIcon('https://esoraidplanner.com/favicon/appicon.jpg');
        $embeds->setAuthorUrl('https://esoraidplanner.com');
        $embeds->setFooterIcon('https://esoraidplanner.com/favicon/appicon.jpg');
        $embeds->setFooterText('ESO Raidplanner by Woeler');

        return $embeds;
    }

    protected function buildText(): string
    {
        return str_replace(['{GUILD_NAME}', '{APPLICANT_NAME}', '{GUILD_URL}'], [$this->subjects['guild']->name, $this->subjects['user']->name, env('APP_URL').'/g/'.$this->subjects['guild']->slug], $this->text);
    }
}
