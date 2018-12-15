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
 * Time: 19:10
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Set.
 *
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property string                          $name
 * @property int                             $version
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $tooltip
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Set whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Set whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Set whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Set whereTooltip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Set whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Set whereVersion($value)
 */
class Set extends Model
{
}
