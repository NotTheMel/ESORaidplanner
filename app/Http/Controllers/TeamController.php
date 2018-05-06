<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 06.05.18
 * Time: 15:49.
 */

namespace App\Http\Controllers;

use App\Guild;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
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

    public function delete(string $slug, int $team_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        $team = Team::query()->find($team_id);
        $team->delete();

        return redirect('/g/'.$slug.'/teams');
    }

    public function addMember(Request $request, string $slug, int $team_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        /** @var Team $team */
        $team = Team::query()->find($team_id);
        $team->addMember($request->input('user_id'), $request->input('class_id'), $request->input('role_id'), $request->input('sets'));

        return redirect('/g/'.$slug.'/team/'.$team->id);
    }

    public function removeMember(string $slug, int $team_id, int $user_id)
    {
        $guild = Guild::query()->where('slug', '=', $slug)->first();

        if (!$guild->isAdmin(Auth::user())) {
            return redirect('/g/'.$guild->slug);
        }

        /** @var Team $team */
        $team = Team::query()->find($team_id);
        $team->removeMember($user_id);

        return redirect('/g/'.$slug.'/team/'.$team->id);
    }
}
