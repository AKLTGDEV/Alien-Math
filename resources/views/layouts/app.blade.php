<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" prefix="og: http://ogp.me/ns#">

<head>

    @if(env('CD_SITE_LIVE') == true)
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-175594964-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-175594964-1');
    </script>
    <script data-ad-client="ca-pub-2250064310795615" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '849190945865574',
                xfbml: true,
                version: 'v9.0'
            });
            FB.AppEvents.logPageView();
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <script src="https://kit.fontawesome.com/db35d80784.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js"></script>

    @else

    <link rel="stylesheet" href="{{ config('app.url') }}/thirdparty/FA/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link href="{{ config('app.url') }}/thirdparty/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="{{ config('app.url') }}/thirdparty/jquery.min.js"></script>
    <script type="text/javascript" src="{{ config('app.url') }}/thirdparty/popper.min.js"></script>
    <script type="text/javascript" src="{{ config('app.url') }}/thirdparty/bootstrap.min.js"></script>

    @endif


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta property="fb:app_id" content="849190945865574" />

    <?php

    use App\meta;
    ?>

    @if(isset($META_TITLE))
    <meta property="og:title" content="{{ meta::title($META_TITLE) }}" />
    <title>{{ meta::title($META_TITLE) }}</title>
    @else
    <meta property="og:title" content="Online Classrooms / {{ config('app.name', 'Crowdoubt') }}" />
    <title>Online Classrooms / {{ config('app.name', 'Crowdoubt') }}</title>
    @endif

    @if(isset($META_DESCRIPTION))
    <meta name="description" content="{{ $META_DESCRIPTION }}">
    @else
    <meta name="description" content="online classrooms">
    @endif

    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:site_name" content="{{ config('app.name', 'Crowdoubt') }}" />
    <meta property="og:image" itemprop="image primaryImageOfPage" content="{{ config('app.url') }}/favicon.png" />

    <link rel="icon" type="image/png" href="{{ config('app.url') }}/favicon.png" />
    <link rel="stylesheet" href="{{ config('app.url') }}/thirdparty/toruskit-free/dist/css/toruskit.min.css">

</head>

<style>
    #brand-logo {
        font-size: 34px;
    }

    #app-navbar {
        /*background: linear-gradient(120deg, #00e4d0, #5983e8);*/
        background: #546e7a;
    }

    body {
        /*background: url('{{ config('app.url') }}/images/bg4.png');*/
        background: #eceff1;
    }

    .nav-link span {
        color: #ffffff;
    }

    .dropdown-toggle:after {
        content: none
    }
</style>

<nav class="navbar navbar-expand-md navbar-dark sticky-top" id="app-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ config('app.url') }}/favicon.png" height="30" alt="CD">
            <div class="d-none d-sm-inline-block">
                {{ config('app.name', 'Crowdoubt') }}
            </div>
        </a>
        <button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navcol-1">
            <?php

            use App\adminlist;
            use App\groups;

            ?>
            <ul class="nav navbar-nav ml-auto">
                @guest
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                </li>
                @else
                <?php
                if (!isset($searchbar)) {
                    $searchbar = false;
                }
                ?>
                @if ($searchbar)
                <li class="nav-item" role="presentation">
                    <form action="{{ route('search') }}">
                        <div class="input-group">
                            <input name="q" type="text" class="form-control" placeholder="Search CrowDoubt">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </li>
                @endif
                @if(adminlist::isadmin(Auth::user()->username) || groups::isoperator(Auth::user()->username))
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ route('admin') }}">
                        <span>
                            Admin
                            <i class="fas fa-user-shield">
                            </i>
                        </span>
                    </a>
                </li>
                @endif

                <li class="nav-item dropdown" role="presentation">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <span>
                            <span class="fa fa-plus">
                            </span>
                        </span>
                    </a>

                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('newpost') }}">
                                Post Question
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('createworksheet') }}">
                                Create Worksheet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('createclassroom') }}">
                                Create Classroom
                            </a>
                        </li>
                        <!--<li>
                            <a class="dropdown-item" href="{{ route('createTS') }}">
                                Create Test Series
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('reqtopics') }}">
                                Request Topics
                            </a>
                        </li> ON HOLD, COME BACK LATER -->
                    </ul>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ route('notifs') }}">
                        <span>
                            <!--Notifications-->
                            <span class="fa fa-bell">
                                <!--Alerts-->
                            </span>
                        </span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ route('explore') }}">
                        <span>
                            <span class="fa fa-compass">
                            </span>
                        </span>
                    </a>
                </li>
                <li class="dropdown nav-item">
                    <a class="nav-link dropdown-toggle" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <img class="rounded-circle" src="{{ config('app.url') }}/user/{{ Auth::user()->username }}/profilepic" height="25" alt="{{ Auth::user()->name }}">
                        <span class="caret">
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li class="nav-item">
                            <a class="dropdown-item" href="{{ config('app.url') }}/user/{{Auth::user()->username}}">
                                {{ Auth::user()->name }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="dropdown-item" href="{{ route('stats') }}">
                                <span>
                                    Stats
                                    <!--<span class="fa fa-bars">
                                    </span>-->
                                </span>
                            </a>
                        </li>
                        <div class="nav-item">
                            <a class="dropdown-item" href="{{ route('qbank_index') }}">
                                Question Bank
                            </a>
                        </div>
                        <li class="nav-item">
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                        </li>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                </li>
            </ul>
            </li>
            @endguest
            </ul>
        </div>
    </div>
</nav>

<body style="height: 100%;">
    <!--<script async src="https://msgose.com/pw/waWQiOjEwNzE0NDcsInNpZCI6MTA3ODIwMywid2lkIjoxNTgzNDgsInNyYyI6Mn0=eyJ.js"></script>-->

    <div id="app">
        @yield('content')
    </div>
</body>

</html>