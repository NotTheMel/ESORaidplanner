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
 * Time: 09:27
 */

namespace App;

use App\Utility\Classes;
use App\Utility\Roles;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Character.
 *
 * @property \App\User $user
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property string                          $name
 * @property int                             $class
 * @property int                             $role
 * @property mixed|null                      $sets
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $public
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Character whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Character whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Character whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Character whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Character wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Character whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Character whereSets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Character whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Character whereUserId($value)
 */
class Character extends Model
{
    protected $fillable = ['name', 'role', 'class', 'sets', 'user_id', 'public'];

    protected $casts = [
        'sets' => 'array',
    ];

    /**
     * Get the user this character belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function sets(): array
    {
        return $this->sets;
    }

    public function class(): string
    {
        return Classes::CLASSES[$this->class];
    }

    public function role(): string
    {
        return Roles::ROLES[$this->role];
    }

    public function delete()
    {
        $signups = Signup::query()
            ->where('character_id', '=', $this->id)
            ->get()->all();

        foreach ($signups as $signup) {
            $signup->character_id = null;
            $signup->save();
        }

        return parent::delete();
    }

    public function setsStringFormatted(): string
    {
        if (!is_array($this->sets)) {
            return '';
        }

        return implode(', ', $this->sets);
    }

    public function isPublic(): bool
    {
        return 1 === $this->public;
    }
}
