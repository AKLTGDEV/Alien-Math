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

                <li class="dropdown nav-item">
                    <a class="nav-link dropdown-toggle" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <img class="rounded-circle" src="{{ config('app.url') }}/user/{{ Auth::user()->username }}/profilepic" height="25" alt="{{ Auth::user()->name }}">
                        <span class="caret">
                        </span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <!--<li class="nav-item">
                            <a class="dropdown-item" href="{{ config('app.url') }}/user/{{Auth::user()->username}}">
                                {{ Auth::user()->name }}
                            </a>
                        </li>-->
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
    <div id="app">

        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-muted">
                        Welcome, {{ $user->name }}
                    </h2>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">

                    <div class="card">
                        <div class="card-header">
                            Student Information
                        </div>
                        <div class="card-body">
                            Grade: {{ $user->grade }} <br>
                            Level: {{ $user->level }}
                        </div>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="card">
                        <div class="card-header">
                            Quiz
                        </div>
                        <div class="card-body">
                            <form action="{{ route('quiz.generate') }}" method="get">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="grade" class="text-muted">Select Grade</label>
                                        <select class="form-control" id="grade" name="grade">
                                            <option value="X">Any</option>
                                            <option value="P1" <?php if($user->grade == "P1"){ echo "selected"; } ?> >Primary 1</option>
                                            <option value="P2" <?php if($user->grade == "P2"){ echo "selected"; } ?> >Primary 2</option>
                                            <option value="P3" <?php if($user->grade == "P3"){ echo "selected"; } ?> >Primary 3</option>
                                            <option value="P4" <?php if($user->grade == "P4"){ echo "selected"; } ?> >Primary 4</option>
                                            <option value="P5" <?php if($user->grade == "P5"){ echo "selected"; } ?> >Primary 5</option>
                                            <option value="P6" <?php if($user->grade == "P6"){ echo "selected"; } ?> >Primary 6</option>

                                            <option value="S1" <?php if($user->grade == "S1"){ echo "selected"; } ?> >Secondary 1</option>
                                            <option value="S2" <?php if($user->grade == "S2"){ echo "selected"; } ?> >Secondary 2</option>
                                            <option value="S3" <?php if($user->grade == "S3"){ echo "selected"; } ?> >Secondary 3</option>
                                            <option value="S4" <?php if($user->grade == "S4"){ echo "selected"; } ?> >Secondary 4</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="difficulty" class="text-muted">Select difficulty</label>
                                        <select class="form-control" id="difficulty" name="difficulty">
                                            <option value="X">Any</option>
                                            <option value="1" <?php if($user->level == "1"){ echo "selected"; } ?> >Easy</option>
                                            <option value="2" <?php if($user->level == "2"){ echo "selected"; } ?> >Medium</option>
                                            <option value="3" <?php if($user->level == "3"){ echo "selected"; } ?> >Hard</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="form-group" id="tag_h">
                                    <label for="topics" class="text-muted">With Topics:</label>
                                    <input class="form-control" type="text" name="topics" data-role="tagsinput" id="topics" placeholder="TODO">
                                </div>

                                <div class="form-group">
                                    <label for="nos" class="text-muted">Number of questions:</label>
                                    <input class="form-control" type="number" name="nos" data-role="tagsinput" id="nos" value="10">
                                </div>

                                <button class="btn btn-md btn-primary" type="submit">
                                    Start Quiz
                                </button>

                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</body>

</html>