<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- FAVICONS -->
    <link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#9366e9">
    <meta name="msapplication-TileImage" content="/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#9366e9">

    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="ESO Raidplanner" />
    <meta property="og:description" content="The raidplanner app for The Elder Scrolls Online." />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:site_name" content="ESO Raidplanner by Woeler" />
    <meta property="og:image" content="https://esoraidplanner.com/favicon/appicon.jpg" />
    <meta property="og:image:secure_url" content="https://esoraidplanner.com/favicon/appicon.jpg" />
    <meta property="og:image:width" content="1024" />
    <meta property="og:image:height" content="1024" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:description" content="The raidplanner app for The Elder Scrolls Online." />
    <meta name="twitter:title" content="ESO Raidplanner" />
    <meta name="twitter:image" content="https://esoraidplanner.com/favicon/appicon.jpg" />

    <!-- Styles -->
    <link href="{{ asset('css/all.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

    <!-- Animation library for notifications   -->
    <link href="{{ asset('css/animate.min.css') }}" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="{{ asset('css/light-bootstrap-dashboard.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/raidplanner.css') }}" rel="stylesheet"/>
    @if(Auth::check())
        @if(Auth::user()->nightmode === 1)
            <link href="{{ asset('css/nightmode.css') }}" rel="stylesheet"/>
        @endif
    @endif


<!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="{{ asset('css/demo.css') }}" rel="stylesheet"/>

    <link href="{{ asset('css/chosen.min.css') }}" rel="stylesheet"/>

    <!--     Fonts and icons     -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="{{ asset('css/pe-icon-7-stroke.css') }}" rel="stylesheet"/>

    <link href="{{ asset('css/buttons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sets.css') }}" rel="stylesheet">

    @if(env('APP_ENV') === 'production')
    <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_ID') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '{{ env('GOOGLE_ANALYTICS_ID') }}');
        </script>
    @endif

    <script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/js/bootstrap.min.js"></script>



</head>
<body>

<div class="wrapper">
    <div class="sidebar"
         @if(Auth::check())
         @if(Auth::user()->nightmode === 0)
         @if (Auth::user()->layout === 1)
         data-color="orange"
         @elseif (Auth::user()->layout === 2)
         data-color="red"
         @elseif (Auth::user()->layout === 3)
         data-color="green"
         @elseif (Auth::user()->layout === 4)
         data-color="blue"
         @elseif (Auth::user()->layout === 5)
         data-color="green"
         @else
         data-color="purple"
         @endif
         @else
         data-color="grey"
         @endif
         @else
         data-color="purple"
            @endif
    >

        <!--   you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple" -->


        <div class="sidebar-wrapper">
            <div class="logo">
                <a href="https://esoraidplanner.com" class="simple-text">
                    ESO Raidplanner
                </a>
            </div>

            <ul class="nav">
                @if (!Auth::guest())
                    <li>
                        <a href="/">
                            <i class="pe-7s-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    @foreach(Auth::user()->guilds() as $guild)
                        <li>
                            <a href="{{ '/g/' . $guild->slug }}">
                                <i class="pe-7s-ribbon"></i>
                                <p>{{ $guild->name }}</p>
                            </a>
                        </li>
                    @endforeach

                    <li>
                        <a href="/guilds">
                            <i class="pe-7s-world"></i>
                            <p>Guild list</p>
                        </a>
                    </li>
                    <li>
                        <a href="/guild/create">
                            <i class="pe-7s-note"></i>
                            <p>Create a Guild</p>
                        </a>
                    </li>
                    <li>
                        <a href="/user/account-settings">
                            <i class="pe-7s-user"></i>
                            <p>User Profile</p>
                        </a>
                    </li>
                @else
                    <li>
                        <a href="/login">
                            <i class="pe-7s-unlock"></i>
                            <p>Login</p>
                        </a>
                    </li>
                    <li>
                        <a href="/register">
                            <i class="pe-7s-add-user"></i>
                            <p>Register</p>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="main-panel
    @if (Auth::check())
    @if(Auth::user()->nightmode === 0)
    @if (Auth::user()->layout === 1)
            rp-main-background-1
@elseif (Auth::user()->layout === 2)
            rp-main-background-2
@elseif (Auth::user()->layout === 3)
            rp-main-background-3
@elseif (Auth::user()->layout === 4)
            rp-main-background-4
@elseif (Auth::user()->layout === 5)
            rp-main-background-5
@else
            rp-main-background-0
@endif
    @else
            rp-main-background-0
@endif
    @else
            rp-main-background-0
    @endif
            ">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse"
                            data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    @if (!Auth::guest())
                        <a class="navbar-brand" href="#">Welcome back {{ Auth::user()->name }}</a>
                    @endif
                </div>
                <div class="collapse navbar-collapse">

                    <ul class="nav navbar-nav navbar-right">
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}"><p>Login</p></a></li>
                            <li><a href="{{ route('register') }}"><p>Register</p></a></li>
                        @else
                            <li>
                                <a href="#">
                                    <p>Nightmode</p></a>
                            </li>
                            <li>
                                <label class="switch">
                                    @if(Auth::user()->nightmode === 0)
                                        {!! Form::checkbox('nightmode', 1, false, ['onchange' => 'nightmode(this)']); !!}
                                    @else
                                        {!! Form::checkbox('nightmode', 1, true, ['onchange' => 'nightmode(this)']); !!}
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                            </li>
                            <li>
                                <a href="/profile/menu">
                                    <p>Account</p>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    <p>Log out</p>
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        @endif
                        <li class="separator hidden-lg hidden-md"></li>
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')

        <footer class="footer">
            <div class="container-fluid">
                <nav class="pull-left">
                    <ul>
                        <li>
                            <a href="/about">
                                About
                            </a>
                        </li>
                        <li>
                            <a href="/faq">
                                FAQ
                            </a>
                        </li>
                        <li>
                            <a href="/changelog">
                                Changelog
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/ESORaidplanner/ESORaidplanner" target="_blank">
                                Contribute
                            </a>
                        </li>
                        <li>
                            <a href="/termsofuse">
                                Terms of Use
                            </a>
                        </li>
                        <li>
                            <a href="https://patreon.com/woeler" target="_blank">
                                Support ESO Raidplanner
                            </a>
                        </li>
                    </ul>
                </nav>
                <p class="copyright pull-right">
                    &copy; {{ date('Y') }} ESO Raidplanner - By <a href="https://woeler.eu" target="_blank">Woeler</a>®
                </p>
            </div>
        </footer>


    </div>
</div>


</body>

<!--   Core JS Files   -->
<script src="{{ asset('js/jquery-1.10.2.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap.min.js') }}" type="text/javascript"></script>

<!--  Checkbox, Radio & Switch Plugins -->
<script src="{{ asset('js/bootstrap-checkbox-radio-switch.js') }}"></script>

<!--  Charts Plugin -->
<script src="{{ asset('js/chartist.min.js') }}"></script>

<!--  Notifications Plugin    -->
<script src="{{ asset('js/bootstrap-notify.js') }}"></script>

<!-- <script src="{{ asset('js/bootstrap-select.js') }}"></script> -->

<!--  Google Maps Plugin    -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>

<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
<script src="{{ asset('js/light-bootstrap-dashboard.js') }}"></script>

<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
<script src="{{ asset('js/demo.js') }}"></script>


<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

<script src="{{ asset('js/all.js') }}"></script>
<script src="{{ asset('js/chosen.jquery.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('a[data-toggle=tooltip]').tooltip();
    });
</script>

<script>
    $(document).ready(function (e) {
        $(".chosen-select").chosen();
    });
</script>

<script>
    $('.selectpicker').selectpicker({
        style: 'btn-info',
        size: 4
    });
</script>

<script>
    $(function () {
        $("#datepicker").datepicker();
    });
</script>

<script>
    function tableSearch() {
        // Declare variables
        var input, filter, table, tr, td, i;
        input = document.getElementById("guild-searchbar");
        filter = input.value.toUpperCase();
        table = document.getElementById("guild-table");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>

<script>
    function nightmode(checkboxElem) {
        if (checkboxElem.checked) {
            window.location.href = "{{ env('APP_URL') . '/profile/nightmode/1?url=' . $_SERVER['REQUEST_URI'] }}";
        } else {
            window.location.href = "{{ env('APP_URL') . '/profile/nightmode/0?url=' . $_SERVER['REQUEST_URI'] }}";
        }
    }
</script>


</html>
