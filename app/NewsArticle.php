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

use App\Utility\UserDateHandler;
use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * App\NewsArticle.
 *
 * @mixin \Eloquent
 *
 * @property int                             $id
 * @property string                          $title
 * @property string                          $content
 * @property string                          $image
 * @property int                             $author_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsArticle whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsArticle whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsArticle whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsArticle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsArticle whereUpdatedAt($value)
 */
class NewsArticle extends Model
{
    protected $table    = 'newsarticles';
    protected $fillable = [
        'title',
        'content',
        'image',
        'author_id',
    ];

    /**
     * Get a human readable date string based on user settings.
     *
     * @return string
     */
    public function getUserHumanReadableDate(): string
    {
        return UserDateHandler::getUserHumanReadableDate(new DateTime($this->{self::CREATED_AT}));
    }

    /**
     * Get the author user of this article.
     *
     * @return User
     */
    public function getAuthor(): User
    {
        return User::query()->find($this->author_id);
    }
}
