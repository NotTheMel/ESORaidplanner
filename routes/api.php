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


Route::post('/checklogin', 'ApiController@checkLogin');

Route::post('/user', 'ApiController@getUser');

Route::post('/sets', 'ApiController@getSets');

Route::post('/sets/version', 'ApiController@getSetsVersion');

Route::post('/guilds', 'ApiController@getGuilds');

Route::post('/timezones', 'ApiController@getTimezones');

Route::post('/characters', 'ApiController@getCharacters');

Route::post('/guild/{guild_id}', 'ApiController@getGuild');

Route::post('/user/create', 'ApiController@createUser');

Route::post('/user/guilds', 'ApiController@getUserGuilds');

Route::post('/user/events', 'ApiController@getUserEvents');

Route::post('/user/signups', 'ApiController@getUserSignups');

Route::post('/user/{user_id}', 'ApiController@getUserInfo');

Route::post('/user/events/signedup', 'ApiController@getUserEventsSignedUp');

Route::post('/guild/{guild_id}/events', 'ApiController@getGuildEvents');

Route::post('/guild/{guild_id}/members', 'ApiController@getGuildMembers');

Route::post('/guild/{guild_id}/members/pending', 'ApiController@getGuildMembersPending');

Route::post('/guild/{guild_id}/requestmembership', 'ApiController@requestGuildMembership');

Route::post('/guild/{guild_id}/leave', 'ApiController@leaveGuild');

Route::post('/guild/{guild_id}/approve/{user_id}', 'ApiController@approveGuildMembership');

Route::post('/guild/{guild_id}/remove/{user_id}', 'ApiController@removeGuildMembership');

Route::post('/guild/{guild_id}/promote/{user_id}', 'ApiController@makeGuildAdmin');

Route::post('/guild/{guild_id}/demote/{user_id}', 'ApiController@removeGuildAdmin');

Route::post('/event/{event_id}/signups', 'ApiController@getEventSignups');

Route::post('/signup/create/{event_id}', 'ApiController@signUpUser');

Route::post('/signup/modify/{event_id}', 'ApiController@editSignup');

Route::post('/signup/delete/{event_id}', 'ApiController@signOffUser');

Route::post('/signup/get/{event_id}', 'ApiController@getSignup');

Route::post('/signup/confirm/{signup_id}', 'ApiController@confirmSignup');

Route::post('/signup/backup/{signup_id}', 'ApiController@backupSignup');

Route::post('/signup/remove/{signup_id}', 'ApiController@removeSignup');

Route::post('/character/create', 'ApiController@createCharacter');

Route::post('/character/modify/{character_id}', 'ApiController@modifyCharacter');

Route::post('/character/delete/{character_id}', 'ApiController@deleteCharacter');

Route::post('/event/create', 'ApiController@createEvent');

Route::post('/event/modify/{event_id}', 'ApiController@modifyEvent');

Route::post('/event/delete/{event_id}', 'ApiController@deleteEvent');



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
