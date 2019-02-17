<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login/discord', 'Auth\LoginController@redirectToProvider')->name('discordAuth');
Route::get('/login/discord/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('/news/{article_id}', 'HomeController@showNews');

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');


Route::group(['middleware' => 'auth'], function () {
    Route::get('/user/account-settings', 'UserController@accountSettingsView')->name('userAccountSettingsView');
    Route::post('/user/account-settings', 'UserController@updateAccountSettings')->name('userUpdateAccountSettings');
    Route::get('/user/profile-settings', 'UserController@profileSettingsView')->name('userProfileSettingsView');
    Route::get('/user/profile/nightmode/{mode}', 'UserController@setNightMode');
    Route::post('/user/profile-settings', 'UserController@updateProfileSettings')->name('userUpdateProfileSettings');
    Route::get('/user/avatar', 'UserController@avatarView')->name('userAvatarView');
    Route::post('/user/avatar', 'UserController@updateAvatar')->name('userUpdateAvatar');
    Route::get('/user/characters', 'UserController@characterListView')->name('userCharacterList');
    Route::get('/user/characters/create', 'CharacterController@createView')->name('characterCreateView');
    Route::post('/user/characters/create', 'CharacterController@create')->name('characterCreate');
    Route::post('/user/characters/update/{character_id}', 'CharacterController@update')->name('characterUpdate');
    Route::get('/user/characters/update/{character_id}', 'CharacterController@updateView')->name('characterUpdateView');
    Route::get('/user/characters/delete/{character_id}', 'CharacterController@delete')->name('characterDelete');
    Route::get('/user/ical', 'UserController@icalView')->name('userIcalView');
    Route::get('/user/guilds', 'UserController@guildsView')->name('userGuildsView');

    Route::get('/guilds', 'GuildController@listView');
    Route::get('/guild/create', 'GuildController@createView')->name('guildCreateView');
    Route::post('/guild/create', 'GuildController@create')->name('guildCreate');

    Route::get('/g/{slug}/deactivated', 'GuildController@inactiveView')->name('guildInactiveView');

    Route::group(['middleware' => ['guild.active']], function () {
        Route::get('/g/{slug}/apply', 'GuildController@applyView')->name('guildApplyView');
        Route::post('/g/{slug}/apply', 'GuildController@apply')->name('guildApply');
        Route::get('/g/{slug}/pending', 'GuildController@pendingView')->name('guildPendingView');
    });
    
    Route::group(['middleware' => ['guild.member']], function () {
        Route::get('/g/{slug}/member/leave', 'GuildController@leave')->name('guildLeave');
    });

    Route::group(['middleware' => ['guild.member', 'guild.active']], function () {
        Route::get('/g/{slug}', 'GuildController@detailView')->name('guildDetailView');
        Route::get('/g/{slug}/pastevents', 'GuildController@pastEventsView')->name('guildPastEventsView');

        Route::post('/g/{slug}/event/{event_id}/signup', 'EventController@signup')->name('eventSignup');
        Route::get('/g/{slug}/event/{event_id}/signoff', 'EventController@signoff')->name('eventSignoff');
        Route::get('/g/{slug}/event/view/{event_id}', 'EventController@detailView')->name('eventDetailView');
        Route::post('/g/{slug}/event/{event_id}/comment/add', 'EventController@addComment')->name('eventAddComment');
        Route::post('/g/{slug}/event/{event_id}/comment/update/{comment_id}', 'EventController@updateComment')->name('eventUpdateComment');
    });

    Route::group(['middleware' => ['guild.admin']], function () {
        Route::get('/g/{slug}/activate', 'GuildController@activate')->name('guildActivate');

        Route::group(['middleware' => ['guild.active']], function () {
            Route::get('/g/{slug}/logs', 'GuildController@logsView')->name('guildLogsView');
            Route::get('/g/{slug}/settings', 'GuildController@settingsView')->name('guildSettingsView');
            Route::get('/g/{slug}/members', 'GuildController@membersView')->name('guildMembersView');
            Route::post('/g/{slug}/settings', 'GuildController@saveSettings')->name('guildSaveSettings');
            Route::get('/g/{slug}/settings/removebot', 'GuildController@disconnectDiscordBot')->name('guildDisconnectDiscordBot');
            Route::get('/g/{slug}/member/approve/{user_id}', 'GuildController@approveMember')->name('guildApproveMember');
            Route::get('/g/{slug}/member/remove/{user_id}', 'GuildController@removeMember')->name('guildRemoveMember');

            Route::get('/g/{slug}/event/create', 'EventController@createView')->name('eventCreateView');
            Route::post('/g/{slug}/event/create', 'EventController@create')->name('eventCreate');
            Route::get('/g/{slug}/event/update/{event_id}', 'EventController@updateView')->name('eventUpdateView');
            Route::post('/g/{slug}/event/update/{event_id}', 'EventController@update')->name('eventUpdate');
            Route::get('/g/{slug}/event/delete/{event_id}', 'EventController@delete')->name('eventDelete');
            Route::get('/g/{slug}/event/postsignups/{event_id}', 'EventController@postSignups')->name('eventPostSignups');
            Route::get('/g/{slug}/event/lock/{event_id}/{status}', 'EventController@lock')->name('eventLock');
            Route::get('/g/{slug}/event/{event_id}/comment/delete/{comment_id}', 'EventController@deleteComment')->name('eventDeleteComment');
            Route::post('/g/{slug}/event/{event_id}/signups/set-status', 'EventController@setSignupStatus')->name('eventSetSignupStatus');

            Route::get('/g/{slug}/repeatable/create', 'RepeatableEventController@createView')->name('repeatableCreateView');
            Route::post('/g/{slug}/repeatable/create', 'RepeatableEventController@create')->name('repeatableCreate');
            Route::get('/g/{slug}/repeatable/update/{repeatable_id}', 'RepeatableEventController@updateView')->name('repeatableUpdateView');
            Route::post('/g/{slug}/repeatable/update/{repeatable_id}', 'RepeatableEventController@update')->name('repeatableUpdate');
            Route::get('/g/{slug}/repeatable/delete/{repeatable_id}', 'RepeatableEventController@delete')->name('repeatableDelete');

            Route::get('/g/{slug}/notification/{notification_id}/test', 'NotificationController@sendTestMessage')->name('notificationSendTest');
            Route::get('/g/{slug}/notification/create', 'NotificationController@messageTypeSelectView')->name('notificationMessageTypeSelectView');
            Route::get('/g/{slug}/notification/create/{message_type}', 'NotificationController@systemTypeSelectView')->name('notificationSystemTypeSelectView');
            Route::get('/g/{slug}/notification/create/{message_type}/{system_type}', 'NotificationController@createView')->name('notificationCreateView');
            Route::post('/g/{slug}/notification/create/{message_type}/{system_type}', 'NotificationController@create')->name('notificationCreate');
            Route::get('/g/{slug}/notification/update/{notification_id}', 'NotificationController@updateView')->name('notificationUpdateView');
            Route::post('/g/{slug}/notification/update/{notification_id}', 'NotificationController@update')->name('notificationUpdate');
            Route::get('/g/{slug}/notification/delete/{notification_id}', 'NotificationController@delete')->name('notificationDelete');
        });

    });

    Route::group(['middleware' => ['guild.owner', 'guild.active']], function () {
        Route::get('/g/{slug}/member/addadmin/{user_id}', 'GuildController@addAdmin')->name('guildAddAdmin');
        Route::get('/g/{slug}/member/removeadmin/{user_id}', 'GuildController@removeAdmin')->name('guildRemoveAdmin');
        Route::get('/g/{slug/member/makeowner', 'GuildController@makeOwner')->name('guildMakeOwner');
        Route::get('/g/{slug}/delete', 'GuildController@deleteConfirmView')->name('guildDeleteConfirmView');
        Route::get('/g/{slug}/delete/confirm', 'GuildController@delete')->name('guildDelete');
    });
});
