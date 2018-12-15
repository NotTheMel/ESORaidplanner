<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 13.10.18
 * Time: 15:33.
 */

namespace App\Notification\Message;

use App\Notification\Notification;
use Woeler\DiscordPhp\Message\DiscordEmbedsMessage;

abstract class AbstractNotificationMessage
{
    const CALL_TYPE = 0;
    const IDEN      = '';

    const CONFIG = [
        'identifier'  => '',
        'name'        => '',
        'description' => '',
    ];

    protected $isTimed = false;

    protected $embeds;

    protected $text;

    protected $subjects;

    protected $notification;

    public function __construct(string $text, Notification $notification, array $subjects = [])
    {
        $this->text         = $text;
        $this->notification = $notification;
        $this->subjects     = $subjects;
    }

    public function getText(): string
    {
        return $this->buildText();
    }

    public function getEmbeds(): DiscordEmbedsMessage
    {
        return $this->buildDiscordEmbeds();
    }

    public function getSubjects(): array
    {
        return $this->subjects;
    }

    public function isTimed(): bool
    {
        return $this->isTimed;
    }

    public function wasCalled(Notification $notification)
    {
        return;
    }

    public function meetsSendingConditions(Notification $notification): bool
    {
        return $this->hasNeededSubjects();
    }

    protected function matchesTags(array $tags1, array $tags2): bool
    {
        if (empty($tags1) && empty($tags2)) {
            return true;
        }
        if (empty($tags1) && !empty($tags2)) {
            return false;
        }
        if (!empty($tags1) && empty($tags2)) {
            return false;
        }

        foreach ($tags1 as $event_tag) {
            foreach ($tags2 as $hook_tag) {
                if (trim($event_tag) === trim($hook_tag)) {
                    return true;
                }
            }
        }

        return false;
    }

    abstract protected function buildDiscordEmbeds(): DiscordEmbedsMessage;

    abstract protected function buildText(): string;

    abstract protected function hasNeededSubjects(): bool;
}
