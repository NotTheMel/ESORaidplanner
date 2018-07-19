<?php
/**
 * This file is part of the ESO-Database project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://eso-database.com
 * Created by woeler
 * Date: 19.07.18
 * Time: 12:30
 */

namespace App;

class GuildLogger
{
    public function guildCreate(Guild $guild, User $creator)
    {
        $log = new LogEntry();
        $log->create($guild->id, $creator->name.' created the guild '.$guild->name.'.');
    }

    public function guildMakeAdmin(Guild $guild, User $admin, User $user)
    {
        $log      = new LogEntry();
        $log->create($guild->id, $admin->name.' promoted '.$user->name.' to admin.');
    }

    public function guildRemoveAdmin(Guild $guild, User $admin, User $user)
    {
        $log = new LogEntry();
        $log->create($guild->id, $admin->name.' demoted '.$user->name.' to member.');
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

    public function guildApproveMembership(Guild $guild, User $admin, User $user)
    {
        $log = new LogEntry();
        $log->create($guild->id, $admin->name.' approved the membership request of '.$user->name.'.');
    }

    public function guildRemoveMembership(Guild $guild, User $admin, User $user)
    {
        $log = new LogEntry();
        $log->create($guild->id, $admin->name.' removed '.$user->name.' from the guild.');
    }

    public function eventCreate(Event $event, User $creator)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id, $creator->name.' created the event '.$event->name.'.');
    }

    public function eventSignup(Event $event, User $user)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id, $user->name.' signed up for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>.');
    }

    public function eventSignupOther(Event $event, User $admin, User $user)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id,
            $admin->name.' signed up '.$user->name.' for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>.');
    }

    public function eventSignoff(Event $event, User $user)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id, $user->name.' signed off for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>.');
    }

    public function eventSignoffOther(Event $event, User $admin, User $user)
    {
        $guild = $this->getGuild($event->guild_id);
        $log   = new LogEntry();
        $log->create($guild->id, $admin->name.' signed off '.$user->name.' for <a href="/g/'.$guild->slug.'/event/'.$event->id.'">'.$event->name.'</a>.');
    }

    private function getGuild(int $guild_id)
    {
        return Guild::query()->find($guild_id);
    }
}
