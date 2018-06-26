<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 06.05.18
 * Time: 15:30.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    protected $fillable = ['guild_id', 'name'];

    /**
     * @return array
     */
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

    /**
     * @return int
     */
    public function getMemberCount(): int
    {
        return DB::table('teams_users')
            ->where('team_id', '=', $this->id)
            ->count() ?? 0;
    }

    /**
     * @param int   $user_id
     * @param int   $class_id
     * @param int   $role_id
     * @param array $sets
     */
    public function addMember(int $user_id, int $class_id, int $role_id, array $sets = [])
    {
        $count = DB::table('teams_users')
            ->where('team_id', '=', $this->id)
            ->where('user_id', '=', $user_id)
            ->count();

        if (count($sets) > 0) {
            $sets_s = implode(', ', $sets);
        } else {
            $sets_s = '';
        }

        if (0 === $count) {
            DB::table('teams_users')->insert([
                'team_id'  => $this->id,
                'user_id'  => $user_id,
                'class_id' => $class_id,
                'role_id'  => $role_id,
                'sets'     => $sets_s,
            ]);
        } else {
            DB::table('teams_users')
                ->where('team_id', '=', $this->id)
                ->where('user_id', '=', $user_id)
                ->update([
                'user_id'  => $user_id,
                'class_id' => $class_id,
                'role_id'  => $role_id,
                'sets'     => $sets_s,
            ]);
        }
    }

    /**
     * @param int $user_id
     */
    public function removeMember(int $user_id)
    {
        DB::table('teams_users')
            ->where('team_id', '=', $this->id)
            ->where('user_id', '=', $user_id)
            ->delete();
    }

    public static function formatForForms(int $guild_id)
    {
        $return     = [];
        $return[''] = 'None';
        $teams      = Team::query()->where('guild_id', '=', $guild_id)
            ->orderBy('name')->get()->all() ?? [];
        foreach ($teams as $team) {
            $return[$team->id] = $team->name;
        }

        return $return;
    }
}
