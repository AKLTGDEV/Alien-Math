@extends('layouts.app')

@section('content')

<link href='//fonts.googleapis.com/css?family=Merriweather|Montserrat:400,700|Dancing+Script:400,700' rel='stylesheet' type='text/css'>
<link href="https://fonts.googleapis.com/css?family=Bellefair|Lemonada|Raleway" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

<style>
    * {
        box-sizing: border-box;
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
    }

    a {
        text-decoration: none;
        -webkit-transition: all .3s ease;
        transition: all .3s ease;
    }

    body {
        /*font-family: "Merriweather", serif;*/
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        font-size: 16px;
        line-height: 1.5;
    }

    .hero {
        text-align: center;
        position: relative;
        height: auto;
        padding-top: 20vh;
        background-size: cover;
        color: #fff;
        overflow: hidden;
    }

    .hero h1 {
        margin: 50px 0 20px;
        font-family: "Montserrat", sans-serif;
        font-size: 35px;
    }

    .hero h1 span {
        font: 700 60px/1 "Dancing Script", cursive;
        display: block;
    }

    .hero h2 {
        margin-left: auto;
        margin-right: auto;
        font-size: 1em;
        font-weight: 400;
        padding: 20px;
    }

    @media only screen and (min-width: 37.5em) {
        .hero h1 {
            font-size: 50px;
        }

        .hero h1 span {
            font-size: 76px;
        }

        .hero h2 {
            width: 76%;
            font-size: 18px;
        }
    }

    .wrapper {
        margin: 0 auto;
        width: 96%;
        max-width: 1120px;
    }

    .section {
        padding: 60px 0;
    }

    .section__title {
        font-size: 24px;
        color: #3b3b58;
        text-align: center;
        /*font-family: "Montserrat", sans-serif;*/
        font-family: "Roboto", "Helvetica", "Arial", sans-serif;
        margin: 14px 0;
    }

    .section__intro {
        display: block;
        text-align: center;
        margin: 0 5% 30px;
    }

    @media only screen and (min-width: 43.75em) {
        .section__intro {
            margin-left: 15%;
            margin-right: 15%;
        }
    }

    .section--cta {
        color: #454545;
        background: #f1f1f1;
    }

    .box {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-flex: 1;
        -ms-flex: 1 0 100%;
        flex: 1 0 100%;
        margin-bottom: 20px;
        background: #f9f9f9;
        border-radius: 1%;
        box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.49), 0 6px 30px 5px rgba(104, 99, 141, 0.49), 0 8px 10px -5px rgb(45, 34, 34);
    }

    .box__grid {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
    }

    .box__more {
        color: #ff4e5c;
    }

    .box__content {
        color: #454545;
        text-align: center;
        padding: 20px;
    }

    .box__content>i {
        color: #3b3b58;
    }

    .box__content:hover .box__more {
        color: #ff3545;
    }

    .box__content:hover .box__more i {
        padding-left: 4px;
        -webkit-transition: all .2s ease-in-out;
        transition: all .2s ease-in-out;
    }

    @media only screen and (min-width: 43.75em) {
        .box {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 30%;
            flex: 0 0 30%;
        }
    }

    .cst-btn {
        color: #fff;
        font-family: "Montserrat", sans-serif;
        text-align: center;
        display: block;
        margin: 0 auto;
        max-width: 300px;
        padding: 12px 18px;
        border-radius: 100px;
        background: #ff4e5c;
    }

    .cst-btn:hover {
        background: #ff3545;
    }

    .image-holder {
        width: 100%;
        height: 90vh;
        background-position: center center;
        background-attachment: fixed;
        background-size: cover;
    }
</style>

<div class="container-fluid" style="padding: 0">
    <div class="row">
        <div class="col-md-9">
            <section class="hero image-holder" style="background-image: url('{{ asset('images/bg-masthead.jpg') }}');">
                <h1>
                    <span>Crowdoubt</span>
                    <div style="margin-top: 2%">Community-driven classrooms</div>
                </h1>
            </section>
        </div>
        <div class="col-md-3">
            <div class="container-fluid" style="padding: 0">
                <div class="row">
                    <div class="col-12">
                        <img src="{{ asset('images/teachers.png') }}" class="img-fluid" alt="Teachers">
                    </div>
                    <div class="col-12">
                        <img src="{{ asset('images/students.png') }}" class="img-fluid" alt="Students">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer bg-light mt-1">
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
                <p class="text-muted small mb-4 mb-lg-0">© Syed Nasim 2020. All Rights Reserved. <a href="https://madewithlove.org.in/startup/crowdoubt" target="_blank">Made with <span style="color: #e74c3c">&hearts;</span> in India</a></p>
            </div>
            <div class="col-lg-6 my-auto h-100 text-center text-lg-right">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"><a href="#"></a></li>
                    <li class="list-inline-item"><a href="https://twitter.com/hatless_hacker"><i class="fa fa-twitter fa-2x fa-fw"></i></a></li>
                    <li class="list-inline-item"><a href="https://www.instagram.com/hatless_hacker"><i class="fa fa-instagram fa-2x fa-fw"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>


@endsection