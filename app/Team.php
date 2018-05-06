<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 06.05.18
 * Time: 15:30
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    protected $fillable = ['guild_id', 'name'];

    public function getMembers(): array
    {
        $members = DB::table('teams_users')
            ->select(['teams_users.*', 'users.name'])
            ->join('users', 'teams_users.user_id', '=', 'users.id')
            ->where('team_id', '=', $this->id)
            ->orderBy('users.name')
            ->get()->all();

        return $members ?? [];
    }
}