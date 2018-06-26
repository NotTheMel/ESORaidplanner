<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 06.05.18
 * Time: 15:49.
 */

namespace App\Http\Controllers;

use App\Guild;
use App\RepeatableEvent;
use App\Set;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * @param Request $request
     * @param string  $slug
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request, string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        $team = new Team(['name' => $request->input('name'), 'guild_id' => $guild->id]);
        $team->save();

        return redirect('/g/'.$slug.'/teams');
    }

    public function new(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        return view('team.create', compact('guild'));
    }

    public function view(string $slug, int $team_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }
        $team = Team::query()->find($team_id);
        if ($team->guild_id !== $guild->id) {
            return redirect('/g/'.$guild->slug);
        }
        $members = [];
        foreach ($guild->getMembers() as $mem) {
            $members[$mem->id] = $mem->name;
        }
        $sets_q = Set::query()
            ->orderBy('name', 'asc')
            ->get();
        $sets = [];

        foreach ($sets_q as $set) {
            $sets[$set->name] = $set->name;
        }

        return view('team.view', compact('guild', 'team', 'members', 'sets'));
    }

    public function list(string $slug)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }
        $teams = Team::query()->where('guild_id', '=', $guild->id)
            ->get()->all() ?? [];

        return view('team.list', compact('guild', 'teams'));
    }

    /**
     * @param string $slug
     * @param int    $team_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete(string $slug, int $team_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        $team = Team::query()->find($team_id);
        if ($team->guild_id !== $guild->id) {
            return redirect('/g/'.$guild->slug);
        }
        RepeatableEvent::query()
            ->where('default_team_id', '=', $team->id)
            ->update(['default_team_id' => null]);

        $team->delete();

        return redirect('/g/'.$slug.'/teams');
    }

    /**
     * @param Request $request
     * @param string  $slug
     * @param int     $team_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addMember(Request $request, string $slug, int $team_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        /** @var Team $team */
        $team = Team::query()->find($team_id);
        if ($team->guild_id !== $guild->id) {
            return redirect('/g/'.$guild->slug);
        }
        $team->addMember($request->input('user_id'), $request->input('class_id'), $request->input('role_id'), $request->input('sets') ?? []);

        return redirect('/g/'.$slug.'/team/'.$team->id);
    }

    /**
     * @param string $slug
     * @param int    $team_id
     * @param int    $user_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function removeMember(string $slug, int $team_id, int $user_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        /** @var Team $team */
        $team = Team::query()->find($team_id);
        if ($team->guild_id !== $guild->id) {
            return redirect('/g/'.$guild->slug);
        }
        $team->removeMember($user_id);

        return redirect('/g/'.$slug.'/team/'.$team->id);
    }
}
