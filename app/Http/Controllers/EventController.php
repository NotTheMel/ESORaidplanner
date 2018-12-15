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
 * Date: 18.09.18
 * Time: 16:29
 */

namespace App\Http\Controllers;

use App\Character;
use App\Comment;
use App\Event;
use App\Guild;
use App\Signup;
use App\Team;
use App\User;
use App\Utility\TagHandler;
use App\Utility\UserDateHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function detailView(string $slug, int $event_id)
    {
        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();
        /** @var Event $event */
        $event  = Event::query()->find($event_id);
        $signup = $event->getSignup(Auth::user()) ?? new Signup();

        return view('event.details', compact('guild', 'event', 'signup'));
    }

    /**
     * Show the event creation page.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createView(string $slug)
    {
        /** @var Guild $guild */
        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();

        return view('event.create', compact('guild'));
    }

    /**
     * Show the event update page.
     *
     * @param string $slug
     * @param int    $event_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateView(string $slug, int $event_id)
    {
        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();
        $event = Event::query()->find($event_id);

        return view('event.update', compact('guild', 'event'));
    }

    /**
     * Create an event.
     *
     * @param Request $request
     * @param string  $slug
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request, string $slug)
    {
        $request->validate([
            'name'   => 'required',
            'year'   => 'required',
            'month'  => 'required',
            'day'    => 'required',
            'hour'   => 'required',
            'minute' => 'required',
        ]);

        /** @var Guild $guild */
        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();
        $event = $guild->createEvent(
            $request->input('name'),
            UserDateHandler::requestToDateTime($request->all(), Auth::user()->clock, new \DateTimeZone(Auth::user()->timezone)),
            $request->input('description'),
            TagHandler::stringToArray($request->input('tags') ?? '')
        );

        if (!empty($request->input('team_id'))) {
            $team = Team::query()->find($request->input('team_id'));
            $event->signupWithTeam($team);
        }

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event->id]));
    }

    /**
     * Update an event.
     *
     * @param Request $request
     * @param string  $slug
     * @param int     $event_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, string $slug, int $event_id)
    {
        $request->validate([
            'name'   => 'required',
            'year'   => 'required',
            'month'  => 'required',
            'day'    => 'required',
            'hour'   => 'required',
            'minute' => 'required',
        ]);

        /** @var Guild $guild */
        $guild = Guild::query()
            ->where('slug', '=', $slug)
            ->first();
        $event = Event::query()->find($event_id);

        $guild->updateEvent(
            $event,
            $request->input('name'),
            UserDateHandler::requestToDateTime($request->all(), Auth::user()->clock, new \DateTimeZone(Auth::user()->timezone)),
            $request->input('description'),
            TagHandler::stringToArray($request->input('tags') ?? '')
        );

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event->id]));
    }

    /**
     * Delete an event.
     *
     * @param string $slug
     * @param int    $event_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws \Exception
     */
    public function delete(string $slug, int $event_id)
    {
        $event = Event::query()->find($event_id);
        $event->delete();

        return redirect(route('guildDetailView', ['slug' => $slug]));
    }

    /**
     * Signup for an event.
     *
     * @param Request $request
     * @param string  $slug
     * @param int     $event_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function signup(Request $request, string $slug, int $event_id)
    {
        if (!empty($request->input('user'))) {
            $user = User::query()->find($request->input('user'));
        } else {
            $user = Auth::user();
        }

        /** @var Event $event */
        $event = Event::query()->find($event_id);
        if (null === $request->input('character')) {
            $event->signup(
                $user,
                (int) $request->input('class'),
                (int) $request->input('role'),
                $request->input('sets') ?? []
            );
        } else {
            $character = Character::query()->find($request->input('character'));
            $event->signupWithCharacter($user, $character);
        }

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event->id]));
    }

    /**
     * Signoff for an event.
     *
     * @param Request $request
     * @param string  $slug
     * @param int     $event_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function signoff(Request $request, string $slug, int $event_id)
    {
        if (!empty($request->input('user'))) {
            $user = User::query()->find($request->input('user'));
        } else {
            $user = Auth::user();
        }

        /** @var Event $event */
        $event = Event::query()->find($event_id);
        $event->signoff($user);

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event->id]));
    }

    /**
     * Lock or unlock the an event.
     *
     * @param string $slug
     * @param int    $event_id
     * @param int    $status
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function lock(string $slug, int $event_id, int $status)
    {
        /** @var Event $event */
        $event = Event::query()->find($event_id);
        $event->lock($status);

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event->id]));
    }

    public function setSignupStatus(Request $request, string $slug, int $event_id)
    {
        if ('Confirm selected' === $request->post('action')) {
            $status = 1;
        } elseif ('Backup selected' === $request->post('action')) {
            $status = 2;
        } elseif ('Delete selected' === $request->post('action')) {
            $status = 'delete';
        } else {
            $status = 0;
        }
        $request->offsetUnset('action');
        /** @var Event $event */
        $event = Event::query()->find($event_id);

        $signups = $event->signups()
            ->whereIn('id', $request->all())
            ->get()->all();

        /** @var Signup $signup */
        foreach ($signups as $signup) {
            if ('delete' === $status) {
                $signup->delete();
            } else {
                $signup->setStatus($status);
            }
        }

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event_id]));
    }

    public function addComment(Request $request, string $slug, int $event_id)
    {
        /** @var Event $event */
        $event = Event::query()->find($event_id);
        $event->addComment(Auth::user(), $request->input('text'));

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event->id]));
    }

    public function updateComment(Request $request, string $slug, int $event_id, int $comment_id)
    {
        /** @var Event $event */
        $event   = Event::query()->find($event_id);
        $comment = Comment::query()->find($comment_id);
        $event->updateComment($comment, $request->input('text'));

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event->id]));
    }

    public function deleteComment(string $slug, int $event_id, int $comment_id)
    {
        /** @var Event $event */
        $event   = Event::query()->find($event_id);
        $comment = Comment::query()->find($comment_id);
        $event->deleteComment($comment);

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event->id]));
    }

    public function postSignups(string $slug, int $event_id)
    {
        $event   = Event::query()->find($event_id);
        $event->sendSignupsNotifications();

        return redirect(route('eventDetailView', ['slug' => $slug, 'event_id' => $event->id]));
    }
}
