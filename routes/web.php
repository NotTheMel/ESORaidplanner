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

Route::get('/', 'HomeController@index');

Route::get('/news/{article_id}', 'NewsController@show');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'UserController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

/*
 *
 * ROUTES FOR GENERAL
 *
 */

Route::get('/changelog', function () {
    return view('changelog');
});

Route::get('/faq', function () {
    return view('faq');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/termsofuse', function () {
    return view('termsofuse');
});


/*
 *
 * ROUTES FOR EVENTS
 *
 */

Route::group(['middleware' => 'auth'], function () {

    /*
     *
     * ROUTES FOR EVENTS
     *
     */

    Route::get('/events', 'EventsController@index')->name('events');

    Route::get('/g/{slug}/event/{id}', 'EventsController@detail');

    Route::post('/g/{slug}/sign/up/{id}', 'EventsController@signUpUser');

    Route::post('/g/{slug}/sign/other/{id}', 'EventsController@signUpOther');

    Route::post('/g/{slug}/sign/off/{id}', 'EventsController@signOffUser');

    Route::post('/g/{slug}/sign/modify/{id}', 'EventsController@modifySignup');

    Route::get('/g/{slug}/events/create', 'EventsController@new');

    Route::post('/g/{slug}/events/create', 'EventsController@create');

    Route::get('/g/{slug}/events/edit/{id}', 'EventsController@show');

    Route::post('/g/{slug}/events/edit/{id}', 'EventsController@edit');

    Route::get('/g/{slug}/events/delete/{id}', 'EventsController@delete');

    Route::get('/g/{slug}/events/lock/{event_id}/{lockstatus}', 'EventsController@changeLockStatus');

    Route::get('/g/{slug}/pastevents', 'GuildController@pastEvents');

    Route::get('/g/{slug}/logs', 'GuildController@logs');


    // Repeatables //
    Route::get('/g/{slug}/repeatable/create', 'RepeatableController@new');

    Route::post('/g/{slug}/repeatable/create', 'RepeatableController@create');

    Route::get('/g/{slug}/repeatable/edit/{repeatable_id}', 'RepeatableController@view');

    Route::post('/g/{slug}/repeatable/edit/{repeatable_id}', 'RepeatableController@edit');

    Route::get('/g/{slug}/repeatable/delete/{repeatable_id}', 'RepeatableController@delete');

    /*
     *
     * ROUTES FOR WEBHOOKS
     *
     */

    Route::get('/hooks', 'HookController@all');

    Route::get('/hooks/calltypeselect', 'HookController@callTypeSelectForm');

    Route::get('/hooks/typeselect/{call_type}', 'HookController@typeSelectForm');

    Route::get('/hooks/create/{call_type}/{type}', 'HookController@new');

    Route::post('/hooks/create/{call_type}/{type}', 'HookController@create');

    Route::get('/hooks/modify/{hook_id}', 'HookController@show');

    Route::post('/hooks/modify/{hook_id}', 'HookController@edit');

    Route::post('/hooks/delete/{id}', 'HookController@delete');

    /*
     *
     * ROUTES FOR GUILDS
     *
     */

    Route::get('/g/{slug}', 'GuildController@detail');

    Route::get('/guild/create', function () {
        return view('guild.create');
    });

    Route::get('/guilds', 'GuildController@listAll');

    Route::post('/guild/create', 'GuildController@create');

    Route::get('/guild/delete/{id}', 'GuildController@deleteConfirm');

    Route::get('/guild/delete/{id}/confirm', 'GuildController@delete');

    Route::post('/g/{slug}/member/request/{id}', 'GuildController@requestMembership');

    Route::post('/g/{slug}/member/approve/{guild_id}/{user_id}', 'GuildController@approveMembership');

    Route::get('/g/{slug}/member/remove/{guild_id}/{user_id}', 'GuildController@removeMembership');

    Route::get('/g/{slug}/member/makeadmin/{user_id}', 'GuildController@makeAdmin');

    Route::get('/g/{slug}/member/removeadmin/{user_id}', 'GuildController@removeAdmin');

    Route::get('/g/{slug}/member/leave', 'GuildController@leave');

    Route::get('/g/{slug}/settings', 'GuildController@settings');

    Route::post('/g/{slug}/settings', 'GuildController@saveSettings');

    Route::get('/g/{slug}/members', 'GuildController@members');



    /*
     *
     * ROUTES FOR COMMENTS
     *
     */

    Route::post('/g/{slug}/event/{event_id}/comment/create', 'CommentController@create');

    Route::post('/g/{slug}/event/{event_id}/comment/modify/{comment_id}', 'CommentController@edit');

    Route::get('/g/{slug}/event/{event_id}/comment/delete/{comment_id}', 'CommentController@delete');

    /*
     *
     * ROUTES FOR Profile
     *
     */

    Route::get('/profile/menu', 'UserController@menuProfilePage');

    Route::get('/profile/characters', 'UserController@profileCharacters');

    Route::get('/profile/membership', 'UserController@profileMembership');

    Route::get('/profile/accountsettings', 'UserController@editProfilePage');

    Route::post('/profile/accountsettings', 'UserController@editProfile');

    Route::get('/profile/profilesettings', 'UserController@profileEdit');

    Route::post('/profile/profilesettings', 'UserController@profileEditPost');

    Route::get('/profile/edit/avatar', 'UserController@avatarEditPage');

    Route::post('/profile/edit/avatar', 'UserController@editAvatar');

    Route::post('/profile/edit/avatar/upload', 'UserController@uploadAvatar');

    Route::get('/profile/nightmode/{mode}', 'UserController@setNightMode');

    /*
     *
     * ROUTES FOR CHARACTERS
     *
     */

    Route::post('/profile/character/create', 'CharacterController@create');

    Route::post('/profile/character/modify/{id}', 'CharacterController@edit');

    Route::post('/profile/character/delete/{id}', 'CharacterController@delete');

    /*
     *
     * ROUTES FOR SIGNUPS
     *
     */

    Route::post('/g/{slug}/event/{event_id}signup/status', 'EventsController@setSignupStatus');

    Route::get('/signup/delete/{slug}/{event_id}/{id}', 'EventsController@deleteSignup');

    /*
     *
     * ROUTES FOR PAGES
     *
     */

    Route::get('/dashboard', 'HomeController@index');
    Route::get('/home', 'HomeController@index');

    Route::get('/patreon/error', 'PatreonController@error');

    Route::get('/patreon/success', 'PatreonController@success');

    Route::get('/patreon/login', 'PatreonController@OAuth');

    Route::get('profile/{user_id}', 'UserController@profile');

});
