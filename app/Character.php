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

namespace App;

use App\Singleton\ClassTypes;
use App\Singleton\RoleTypes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Character.
 */
class Character extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'class',
        'role',
        'sets',
        'public',
    ];

    public function delete()
    {
        Signup::query()
            ->where('character_id', '=', $this->id)
            ->update(['character_id' => null]);

        return parent::delete();
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return ClassTypes::getClassName($this->class);
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return RoleTypes::getRoleName($this->role);
    }

    /**
     * @return string
     */
    public function getSetsFormatted(): string
    {
        if (empty($this->sets)) {
            return '';
        }
        $sets = explode(', ', $this->sets);

        $setsFound    = [];
        $setsNotFound = [];

        $string = ''
        ;
        foreach ($sets as $set) {
            $new = Set::query()->whereRaw('LOWER(`name`) LIKE "%'.strtolower($set).'%"')->first();
            if (!empty($new)) {
                $setsFound[] = $new;
            } else {
                $setsNotFound[] = $set;
            }
        }

        foreach ($setsFound as $set) {
            $string .= '<a href="https://www.eso-sets.com/set/'.$set->id.'" target="_blank">'.$set->name.'</a>, ';
        }

        $string .= implode(', ', $setsNotFound);

        if (', ' === substr($string, -2, 2)) {
            $string = substr($string, 0, -2);
        }

        return $string;
    }
}
