<?php

namespace App\Utility;

class TagHandler
{
    public static function stringToArray(string $tags): array
    {
        $array = explode(',', $tags);

        foreach ($array as $tag) {
            $tag = trim($tag);
        }

        return $array ?? [];
    }
}
