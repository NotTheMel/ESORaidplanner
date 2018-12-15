<?php
/**
 * Created by PhpStorm.
 * User: woeler
 * Date: 12.10.18
 * Time: 16:37.
 */

namespace App\Utility;

use Illuminate\Support\Facades\DB;

class AvatarHelper
{
    public static function all()
    {
        return DB::table('avatars')->get()->all();
    }
}
