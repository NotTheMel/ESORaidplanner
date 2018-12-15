<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 13.10.18
 * Time: 16:40.
 */

namespace App\Notification\Message;

use App\Notification\System\AbstractNotificationSystem;
use Woeler\DiscordPhp\Message\DiscordEmbedsMessage;

class TestMessage extends AbstractNotificationMessage
{
    const CALL_TYPE = 0;
    const IDEN      = 'test';
    const CONFIG    = [
        'identifier'  => 'test',
        'name'        => 'Test message',
        'description' => 'Sends a message when the test button is pressed.',
    ];

    public function hasNeededSubjects(): bool
    {
        return true;
    }

    protected function buildDiscordEmbeds(): DiscordEmbedsMessage
    {
        $embeds = new DiscordEmbedsMessage();
        $embeds->setUsername('ESO Raidplanner');
        $embeds->setAvatar('https://esoraidplanner.com'.AbstractNotificationSystem::AVATAR_URL);
        $embeds->setTitle('Test Message');
        $embeds->setUrl('https://esoraidplanner.com/');
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
        return 'This is a test message sent by ESO Raidplanner for notification: '.$this->text.'.';
    }
}
