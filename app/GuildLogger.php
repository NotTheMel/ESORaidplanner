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
    private $guild;
    private $event;

    public function __construct(Guild $guild, Event $event = null)
    {
        $this->guild = $guild;
        $this->event = $event;
    }

    public function guildCreate(User $creator)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $creator->name.' created the guild '.$guild->name.'.');
    }

    public function guildMakeAdmin(User $admin, User $user)
    {
        $log      = new LogEntry();
        $log->create($this->guild->id, $admin->name.' promoted '.$user->name.' to admin.');
    }

    public function guildRemoveAdmin(User $admin, User $user)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $admin->name.' demoted '.$user->name.' to member.');
    }

    public function guildLeave(User $user)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $user->name.' left the guild.');
    }

    public function guildRequestMembership(User $user)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $user->name.' requested membership.');
    }

    public function guildApproveMembership(User $admin, User $user)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $admin->name.' approved the membership request of '.$user->name.'.');
    }

    public function guildRemoveMembership(User $admin, User $user)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $admin->name.' removed '.$user->name.' from the guild.');
    }

    public function eventCreate(User $creator)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $creator->name.' created the event '.$this->event->name.'.');
    }

    public function eventSignup(User $user)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $user->name.' signed up for <a href="/g/'.$this->guild->slug.'/event/'.$this->event->id.'">'.$this->event->name.'</a>.');
    }

    public function eventSignupOther(User $admin, User $user)
    {
        $log = new LogEntry();
        $log->create($this->guild->id,
            $admin->name.' signed up '.$user->name.' for <a href="/g/'.$this->guild->slug.'/event/'.$this->event->id.'">'.$this->event->name.'</a>.');
    }

    public function eventSignoff(User $user)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $user->name.' signed off for <a href="/g/'.$this->guild->slug.'/event/'.$this->event->id.'">'.$this->event->name.'</a>.');
    }

    public function eventSignoffOther(User $admin, User $user)
    {
        $log = new LogEntry();
        $log->create($this->guild->id, $admin->name.' signed off '.$user->name.' for <a href="/g/'.$this->guild->slug.'/event/'.$this->event->id.'">'.$this->event->name.'</a>.');
    }
}
