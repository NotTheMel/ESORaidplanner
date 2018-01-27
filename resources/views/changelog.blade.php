@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">ESO Raidplanner Changelog</h4>
                        </div>
                        <div class="content">

                            <h2>Version 0.7.0</h2>
                            <h5>December 15th 2017</h5>
                            <ul>
                                <li>Fixed an issue that would cause an error when approving guild members</li>
                                <li>Added an extra step of confirmation before you can delete a guild</li>
                                <li>Added a logging system to all guild related events. ONly admins can read logs</li>
                            </ul>

                            <h2>Version 0.6.3</h2>
                            <h5>December 15th 2017</h5>
                            <ul>
                                <li>Implemented a guard that checks if your guild slug has the correct format, and gives
                                    an error if it does not
                                </li>
                                <li>Implemented a guard that checks if your Discord or Slack hook has the correct
                                    format, and gives an error if it does not
                                </li>
                                <li>Fixed a bug that could cause a user to sign up for the same guild multiple times
                                </li>
                            </ul>

                            <h2>Version 0.6.2</h2>
                            <h5>December 11th 2017</h5>
                            <ul>
                                <li>Changelog, about and faq pages are now visible for users that are not logged in</li>
                                <li>fixed a bug that prevented notifications in Discord, Slack and Telegram from being
                                    sent
                                </li>
                            </ul>


                            <h2>Version 0.6.1</h2>
                            <h5>November 14th 2017</h5>
                            <ul>
                                <li>Move guild settings to a separate page only accessible for admins</li>
                                <li>Add option to create notifications that trigger when an event is created</li>
                                <li>Add changelog page</li>
                                <li>Add about page</li>
                                <li>Add FAQ page</li>
                            </ul>


                            <h2>Version 0.6.0</h2>
                            <ul>
                                <li>Initial release</li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection