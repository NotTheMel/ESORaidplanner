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

Route::post('/telegram', 'TelegramController@exec');

Route::post('/sets/version', 'Api\SetController@getVersion');

Route::post('/timezones', 'Api\TimeZoneController@all');

Route::post('/user/create', 'Api\UserController@create');

Route::post('/news', 'Api\NewsController@all');

Route::post('/news/{article_id}', 'Api\NewsController@get');

Route::group(['middleware' => 'auth.api'], function () {
    Route::post('/characters', 'Api\CharacterController@all');

    Route::post('/guild/{guild_id}', 'Api\GuildController@get');

    Route::post('/guilds', 'Api\GuildController@all');

    Route::post('/checklogin', 'Api\ApiController@checkLogin');

    Route::post('/user', 'Api\UserController@get');

    Route::post('/sets', 'Api\SetController@all');

    Route::post('/user/guilds', 'Api\UserController@getGuilds');

    Route::post('/user/guilds/pending', 'Api\UserController@getGuildsPending');

    Route::post('/user/events', 'Api\UserController@getEvents');

    Route::post('/user/signups', 'Api\UserController@getSignups');

    Route::post('/user/{user_id}', 'Api\UserController@getInfo');

    Route::post('/user/events/signedup', 'Api\UserController@getEventsSignedUp');

    Route::post('/user/onesignal/add', 'Api\UserController@setOnesignal');

    Route::post('/user/onesignal/remove', 'Api\UserController@deleteOnesignal');

    Route::post('/guild/{guild_id}/events', 'Api\GuildController@getEvents');

    Route::post('/guild/{guild_id}/members', 'Api\GuildController@getMembers');

    Route::post('/guild/{guild_id}/members/pending', 'Api\GuildController@getMembersPending');

    Route::post('/guild/{guild_id}/requestmembership', 'Api\GuildController@requestMembership');

    Route::post('/guild/{guild_id}/leave', 'Api\GuildController@leave');

    Route::post('/guild/{guild_id}/approve/{user_id}', 'Api\GuildController@approveMembership');

    Route::post('/guild/{guild_id}/remove/{user_id}', 'Api\GuildController@removeMembership');

    Route::post('/guild/{guild_id}/promote/{user_id}', 'Api\GuildController@makeAdmin');

    Route::post('/guild/{guild_id}/demote/{user_id}', 'Api\GuildController@removeAdmin');

    Route::post('/event/{event_id}/signups', 'Api\EventController@allSignups');

    Route::post('/signup/create/{event_id}', 'Api\EventController@createSignup');

    Route::post('/signup/modify/{event_id}', 'Api\EventController@editSignup');

    Route::post('/signup/delete/{event_id}', 'Api\EventController@deleteSignup');

    Route::post('/signup/get/{event_id}', 'Api\EventController@getSignup');

    Route::post('/signup/confirm/{signup_id}', 'Api\EventController@confirmSignup');

    Route::post('/signup/backup/{signup_id}', 'Api\EventController@backupSignup');

    Route::post('/signup/remove/{signup_id}', 'Api\EventController@deleteSignupOther');

    Route::post('/character/create', 'Api\CharacterController@create');

    Route::post('/character/modify/{character_id}', 'Api\CharacterController@edit');

    Route::post('/character/delete/{character_id}', 'Api\CharacterController@delete');

    Route::post('/event/create', 'Api\EventController@create');

    Route::post('/event/modify/{event_id}', 'Api\EventController@edit');

    Route::post('/event/delete/{event_id}', 'Api\EventController@delete');
});

Route::group(['middleware' => ['discord.token']], function () {
    Route::post('/discord/setup', 'Api\Discord\DiscordController@setup');
});

Route::group(['middleware' => ['discord.token', 'discord']], function () {
    Route::post('/discord/signup', 'Api\Discord\DiscordController@signUp');
    Route::post('/discord/signoff', 'Api\Discord\DiscordController@signOff');
    Route::post('/discord/events', 'Api\Discord\DiscordController@listEvents');
});