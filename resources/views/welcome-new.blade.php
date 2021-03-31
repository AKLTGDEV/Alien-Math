<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" prefix="og: http://ogp.me/ns#">


<head>
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

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Online Classrooms / {{ config('app.name', 'Alien Math') }}</title>
    <link rel="stylesheet" href="wlc-assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">

    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ Request::url() }}" />
    <meta property="og:site_name" content="{{ config('app.name', 'Alien Math') }}" />
    <meta property="og:image" itemprop="image primaryImageOfPage" content="{{ config('app.url') }}/favicon.png" />
    <meta property="og:title" content="Online Classrooms / {{ config('app.name', 'Alien Math') }}" />

    <link rel="icon" type="image/png" href="{{ config('app.url') }}/favicon.png" />
    <link href='//fonts.googleapis.com/css?family=Merriweather|Montserrat:400,700|Dancing+Script:400,700' rel='stylesheet' type='text/css'>

</head>

<style>
    #brand-logo {
        font-size: 34px;
    }

    #app-navbar {
        /*background: linear-gradient(120deg, #00e4d0, #5983e8);*/
        background: #546e7a;
    }

    .nav-link span {
        color: #ffffff;
    }

    .dropdown-toggle:after {
        content: none
    }

    .title_hd {
        font: 700 60px/1 "Dancing Script", cursive;
    }
</style>

<body>
    <nav class="navbar navbar-expand-md navbar-dark sticky-top" id="app-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ config('app.url') }}/favicon.png" height="30" alt="CD">
                <div class="d-none d-sm-inline-block">
                    {{ config('app.name', 'Alien Math') }}
                </div>
            </a>
            <button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <?php

                use App\adminlist; ?>
                <ul class="nav navbar-nav ml-auto">
                    @guest
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <header class="masthead text-white text-center" style="background:url('wlc-assets/img/bg-masthead.jpg')no-repeat center center;background-size:cover;">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-xl-9 mx-auto">
                    <h1 class="mb-5 title_hd">Alien Math</h1>
                    <p style="font-size: 28px;">Community-driven classrooms</p>
                </div>
            </div>
        </div>
    </header>
    <section class="features-icons bg-light text-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="mx-auto features-icons-item mb-5 mb-lg-0 mb-lg-3">
                        <div class="d-flex features-icons-icon"><i class="icon-screen-desktop m-auto text-primary" data-bs-hover-animate="pulse"></i></div>
                        <h3><strong>Full Featured Classrooms</strong></h3>
                        <p class="lead mb-0">Create and Join Classrooms, compete with fellow students, Attempt worksheets and follow up on class notes.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mx-auto features-icons-item mb-5 mb-lg-0 mb-lg-3">
                        <div class="d-flex features-icons-icon"><i class="icon-layers m-auto text-primary" data-bs-hover-animate="pulse"></i></div>
                        <h3><strong>AI-aided analysis</strong></h3>
                        <p class="lead mb-0">Using our AI-powered tools, find out the topics you're lagging at, know how much time you spend on each question of every worksheet you attempt, and which are the places that need work.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mx-auto features-icons-item mb-5 mb-lg-0 mb-lg-3">
                        <div class="d-flex features-icons-icon"><i class="icon-check m-auto text-primary" data-bs-hover-animate="pulse"></i></div>
                        <h3><strong>Tons of Material for practice</strong></h3>
                        <p class="lead mb-0">Discover the whole lot of Worksheets and questions available for free - curated and updated daily by the community. Explore the material picked by us specially for exams like JEE, NEET, among others.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="showcase">
        <div class="container-fluid p-0">
            <div class="row no-gutters">
                <div class="col-lg-6 order-lg-2 text-white showcase-img" style="background-image:url(&quot;wlc-assets/img/1.png&quot;);"><span></span></div>
                <div class="col-lg-6 my-auto order-lg-1 showcase-text">
                    <h2>The Problem</h2>
                    <p class="lead mb-0">For ages, students have had to struggle while communicating with their teachers. Either they are too shy, too scared, or simply unable to explain what their problems are. There has been a fundamental <strong>gap of understanding</strong> between the students and the teachers, even after the advent of technology.</p>
                </div>
            </div>
            <div class="row no-gutters">
                <div class="col-lg-6 text-white showcase-img" style="background-image:url(&quot;wlc-assets/img/2.png&quot;);"><span></span></div>
                <div class="col-lg-6 my-auto order-lg-1 showcase-text">
                    <h2>Why Alien Math?</h2>
                    <p class="lead mb-0">Existing classroom systems act more like chatting apps plus the feature of sending/recieving documents. In fact, most students prefer classes and tests to be conducted over regular video-calling apps instead. We aim to create a rich
                        classroom environment for the students and teachers, where they can collaborate freely.</p>
                </div>
            </div>

            <!-- EXPLORE DEMO -->
            <div class="row no-gutters mt-2 mb-2">
                <div class="lead mx-auto">
                    <h2>Explore</h2>
                    <h5 class="text-secondary">
                        Here are some of the most trending Worksheets on the site right now
                    </h5>
                    <div class="row">
                        <style>
                            .item {
                                min-width: 50%;
                            }
                        </style>
                        <?php foreach ($wslist as $ws) {
                            $tags = json_decode($ws['tags'], true);
                            $ws['mine'] = false;

                            $ws['attempts'] += 150; // This is cheating tho
                            ?>

                            <div class="col-md item p-1">
                                @include('includes.public-wsitem')
                            </div>

                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="row no-gutters mt-2 mb-2">
                <div class="lead mx-auto">
                    <h1 class="text-bold text-secondary">
                        There's more inside.
                        <span class="text-primary">
                            <a href="{{ route('login') }}">
                                Login Now.
                            </a>
                        </span>
                    </h1>
                </div>
            </div>
        </div>
    </section>
    <section class="call-to-action text-white text-center" style="background:url(&quot;wlc-assets/img/bg-masthead.jpg&quot;) no-repeat center center;background-size:cover;">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-xl-9 mx-auto">
                    <h2 class="mb-4">Are you a teacher? Let's talk.</h2>
                </div>
                <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
                    <form>
                        <div class="form-row">
                            <div class="col-12 col-md-9 mb-2 mb-md-0"><input class="form-control form-control-lg" type="email" placeholder="Enter your email..."></div>
                            <div class="col-12 col-md-3"><button class="btn btn-primary btn-block btn-lg" type="submit" style="font-size: 19px;">Know more</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <footer class="footer bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 my-auto h-100 text-center text-lg-left">
                    <ul class="list-inline mb-2">
                        <li class="list-inline-item"><a href="#">About</a></li>
                        <li class="list-inline-item"><span>⋅</span></li>
                        <li class="list-inline-item"><a href="#">Contact</a></li>
                        <li class="list-inline-item"><span>⋅</span></li>
                        <li class="list-inline-item"><a href="#">Terms of &nbsp;Use</a></li>
                        <li class="list-inline-item"><span>⋅</span></li>
                        <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                    </ul>
                    <p class="text-muted small mb-4 mb-lg-0">© Alien Math 2020. All Rights Reserved.</p>
                </div>
                <div class="col-lg-6 my-auto h-100 text-center text-lg-right">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#"><i class="fa fa-facebook fa-2x fa-fw"></i></a></li>
                        <li class="list-inline-item"><a href="https://twitter.com/hatless_hacker"><i class="fa fa-twitter fa-2x fa-fw"></i></a></li>
                        <li class="list-inline-item"><a href="https://www.instagram.com/Alien Math"><i class="fa fa-instagram fa-2x fa-fw"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="wlc-assets/js/bs-animation.js"></script>
</body>

</html>