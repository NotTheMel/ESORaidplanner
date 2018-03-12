<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/all.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/raidplanner.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

    <!-- Animation library for notifications   -->
    <link href="{{ asset('css/animate.min.css') }}" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="{{ asset('css/light-bootstrap-dashboard.css') }}" rel="stylesheet"/>


    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="{{ asset('css/demo.css') }}" rel="stylesheet"/>

    <link href="{{ asset('css/chosen.min.css') }}" rel="stylesheet"/>

    <link href="https://github.com/pidennis/eso-widgets/blob/master/eso-widgets.min.css" rel="stylesheet">


    <!--     Fonts and icons     -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="{{ asset('css/pe-icon-7-stroke.css') }}" rel="stylesheet"/>

    <link href="{{ asset('css/raidplanner.css') }}" rel="stylesheet"/>

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



</head>
<body>

<div class="wrapper">
    <div class="sidebar"
         @if(Auth::check())
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
                    @foreach(Auth::user()->getGuilds() as $guild)
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
                        <a href="/hooks">
                            <i class="pe-7s-bell"></i>
                            <p>Notification Center</p>
                        </a>
                    </li>
                    <li>
                        <a href="/profile/menu">
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
                    &copy;
                    <script>document.write(new Date().getFullYear())</script>
                    ESO Raidplanner - By Woeler
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
<script src="https://www.elderscrollsbote.de/esodb/tooltips.js"></script>

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


</html>
