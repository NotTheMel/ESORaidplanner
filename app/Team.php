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
 * Date: 12.09.18
 * Time: 08:53
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Team.
 *
 * @property \App\Guild $guild
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property int                             $guild_id
 * @property string                          $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Team whereUpdatedAt($value)
 */
class Team extends Model
{
    const X_REF_USERS = 'teams_users';

    protected $fillable = ['name', 'guild_id'];

    public function delete()
    {
        DB::table(self::X_REF_USERS)
            ->where('team_id', '=', $this->id)
            ->delete();

        return parent::delete();
    }

    /**
     * Get all users in this team.
     *
     * @return array
     */
    public function users()
    {
        return User::query()
            ->select(['users.*', self::X_REF_USERS.'.class_id', self::X_REF_USERS.'.role_id', self::X_REF_USERS.'.sets'])
            ->join(self::X_REF_USERS, 'users.id', self::X_REF_USERS.'.user_id')
            ->where(self::X_REF_USERS.'.team_id', '=', $this->id)
            ->orderBy('users.name')
            ->get()->all();
    }

    public function user(User $user)
    {
        return User::query()
            ->select(['users.*', self::X_REF_USERS.'.class_id', self::X_REF_USERS.'.role_id', self::X_REF_USERS.'.sets'])
            ->join(self::X_REF_USERS, 'users.id', self::X_REF_USERS.'.user_id')
            ->where(self::X_REF_USERS.'.team_id', '=', $this->id)
            ->where('user_id', '=', $user->id)
            ->first();
    }

    /**
     * Get the guild this team belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guild()
    {
        return $this->belongsTo('App\Guild');
    }

    public function addMember(User $user, int $class, int $role, array $sets = [])
    {
        DB::table(self::X_REF_USERS)->insert([
            'team_id'  => $this->id,
            'user_id'  => $user->id,
            'class_id' => $class,
            'role_id'  => $role,
            'sets'     => json_encode($sets),
        ]);
    }

    public function removeMember(User $user)
    {
        DB::table(self::X_REF_USERS)
            ->where('team_id', '=', $this->id)
            ->where('user_id', '=', $user->id)
            ->delete();
    }

    public function updateMember(User $user, int $class, int $role, array $sets = [])
    {
        DB::table(self::X_REF_USERS)
            ->where('team_id', '=', $this->id)
            ->where('user_id', '=', $user->id)
            ->update([
                'class_id' => $class,
                'role_id'  => $role,
                'sets'     => json_encode($sets),
            ]);
    }

    public function isMember(User $user): bool
    {
        return 1 === DB::table(self::X_REF_USERS)
                ->where('team_id', '=', $this->id)
                ->where('user_id', '=', $user->id)
                ->count();
    }
}
