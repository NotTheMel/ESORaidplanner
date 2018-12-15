<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/telegram', 'Api\Telegram\TelegramController@exec');

Route::get('/ical/guild/{uid}', 'Api\Ical\IcalController@guild');
Route::get('/ical/user/{uid}', 'Api\Ical\IcalController@user');


Route::group(['middleware' => ['discord.plain']], function () {
    Route::post('/discord/last-activity', 'Api\Discord\DiscordController@getLastActivity');
});
Route::group(['middleware' => ['discord.token']], function () {
    Route::post('/discord/setup', 'Api\Discord\DiscordController@setup');
    Route::post('/discord/help', 'Api\Discord\DiscordController@help');
});
Route::group(['middleware' => ['discord.token', 'discord']], function () {
    Route::post('/discord/signup', 'Api\Discord\DiscordController@signUp');
    Route::post('/discord/signoff', 'Api\Discord\DiscordController@signOff');
    Route::post('/discord/events', 'Api\Discord\DiscordController@listEvents');
    Route::post('/discord/status', 'Api\Discord\DiscordController@status');
    Route::post('/discord/signups', 'Api\Discord\DiscordController@signups');
});

/*
 * User based API
 */

Route::post('/u/timezones', 'Api\UserBased\UserController@timeZones');
Route::post('/u/user/create', 'Api\UserBased\UserController@create');

Route::group(['middleware' => ['api.user.auth']], function () {
    Route::post('/u/login', 'Api\UserBased\UserController@self');
    Route::post('/u/self', 'Api\UserBased\UserController@self');
    Route::post('/u/user/{user_id}', 'Api\UserBased\UserController@getPublicInfo');
    Route::post('/u/self/guilds', 'Api\UserBased\UserController@getGuilds');
    Route::post('/u/self/guilds/pending', 'Api\UserBased\UserController@getGuildsPending');
    Route::post('/u/guilds', 'Api\UserBased\GuildController@all');
    Route::post('/u/guild/{guild_id}/requestmembership', 'Api\UserBased\GuildController@requestMembership');

    Route::post('/u/characters', 'Api\UserBased\UserController@getCharacters');
    Route::post('/u/character/create', 'Api\UserBased\CharacterController@create');
    Route::post('/u/character/update/{character_id}', 'Api\UserBased\CharacterController@update');
    Route::post('/u/character/delete/{character_id}', 'Api\UserBased\CharacterController@delete');

    Route::post('/u/sets', 'Api\UserBased\SetController@all');
    Route::post('/u/sets/version', 'Api\UserBased\SetController@getVersion');

    Route::post('/u/self/events', 'Api\UserBased\UserController@getEvents');
    Route::post('/u/self/signups', 'Api\UserBased\UserController@getSignups');

    Route::group(['middleware' => ['api.user.guild.member']], function () {
        Route::post('/u/guild/{guild_id}', 'Api\UserBased\GuildController@get');
        Route::post('/u/guild/{guild_id}/members', 'Api\UserBased\GuildController@getMembers');
        Route::post('/u/guild/{guild_id}/members/pending', 'Api\UserBased\GuildController@getPendingMembers');
        Route::post('/u/guild/{guild_id}/events', 'Api\UserBased\GuildController@getEvents');
        Route::post('/u/guild/{guild_id}/leave', 'Api\UserBased\GuildController@leave');
        Route::post('/u/event/{event_id}', 'Api\UserBased\EventController@get');
        Route::post('/u/event/{event_id}/signups', 'Api\UserBased\EventController@getSignups');

        Route::post('/u/signup/create/{event_id}', 'Api\UserBased\EventController@signup');
        Route::post('/u/signup/update/{event_id}', 'Api\UserBased\EventController@signup');
        Route::post('/u/signup/delete/{event_id}', 'Api\UserBased\EventController@signoff');

        Route::group(['middleware' => ['api.user.guild.admin']], function () {
            Route::post('/u/guild/{guild_id}/approve/{user_id}', 'Api\UserBased\GuildController@approveMembership');
            Route::post('/u/guild/{guild_id}/remove/{user_id}', 'Api\UserBased\GuildController@removeMembership');
            Route::post('/u/event/create', 'Api\UserBased\EventController@create');
            Route::post('/u/event/update/{event_id}', 'Api\UserBased\EventController@update');
            Route::post('/u/event/delete/{event_id}', 'Api\UserBased\EventController@delete');
            Route::post('/u/signup/status/{signup_id}/{status}', 'Api\UserBased\EventController@setSignupStatus');

            Route::group(['middleware' => ['api.user.guild.owner']], function () {
                Route::post('/u/guild/{guild_id}/promote/{user_id}', 'Api\UserBased\GuildController@promoteMember');
                Route::post('/u/guild/{guild_id}/demote/{user_id}', 'Api\UserBased\GuildController@demoteMember');
            });
        });
    });
});
