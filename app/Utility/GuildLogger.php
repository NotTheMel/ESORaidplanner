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

namespace App\Utility;

use App\Event;
use App\Guild;
use App\LogEntry;
use App\User;

class GuildLogger
{
    public function guildCreate(Guild $guild, User $creator)
    {
        $log = new LogEntry();
        $log->create($guild->id, $creator->name.' created the guild '.$guild->name.'.');
    }

    public function guildMakeAdmin(Guild $guild, User $user)
    {
        $log      = new LogEntry();
        $log->create($guild->id, $user->name.' was promoted to admin.');
    }

    public function guildRemoveAdmin(Guild $guild, User $user)
    {
        $log = new LogEntry();
        $log->create($guild->id, $user->name.' was demoted to member.');
    }

    public function guildLeave(Guild $guild, User $user)
    {
        $log = new LogEntry();
        $log->create($guild->id, $user->name.' left the guild.');
    }

    public function guildRequestMembership(Guild $guild, User $user)
    {
        $log = new LogEntry();
        $log->create($guild->id, $user->name.' requested membership.');
    }

    public function guildApproveMembership(Guild $guild, User $user)
    {
        $log = new LogEntry();
        $log->create($guild->id, 'Membership of '.$user->name.' was approved.');
    }

    public function guildRemoveMembership(Guild $guild, User $user)
    {
        $log = new LogEntry();
        $log->create($guild->id, $user->name.' was removed from the guild.');
    }

    public function eventCreate(Event $event)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id, 'Event '.$event->name.' was created.');
    }

    public function eventSignup(Event $event, User $user)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id, $user->name.' signed up for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>.');
    }

    public function eventSignoff(Event $event, User $user)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id, $user->name.' signed off for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>.');
    }

    public function eventSignoffOther(Event $event, User $user)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id, $user->name.' was removed from <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>.');
    }

    public function eventDelete(Event $event)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id, 'Event '.$event->name.' was deleted.');
    }

    public function addDiscordBot(Guild $guild)
    {
        $log   = new LogEntry();
        $log->create($guild->id, 'Discord bot was added to '.$guild->name.'.');
    }

    public function transferOwnership(Guild $guild, User $user)
    {
        $log   = new LogEntry();
        $log->create($guild->id, 'Ownership of '.$guild->name.' passed to '.$user->name.'.');
    }

    private function getGuild(int $guild_id)
    {
        return Guild::query()->find($guild_id);
    }
}
