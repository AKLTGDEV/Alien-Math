@extends('layouts.app')

@section('content')

<style>
    #content {
        background: #fff;
        margin-top: 1%;
        margin-bottom: 1%;
    }

    .unseen {
        border: 1px dashed black;
        background: #eceff1;
    }
</style>

<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/bs-charts.js"></script>
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/chart.min.js"></script>

<script>
    (function($) {
        "use strict"; // Start of use strict

        // Toggle the side navigation
        $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
            $("body").toggleClass("sidebar-toggled");
            $(".sidebar").toggleClass("toggled");
            if ($(".sidebar").hasClass("toggled")) {
                $('.sidebar .collapse').collapse('hide');
            };
        });

        // Close any open menu accordions when window is resized below 768px
        $(window).resize(function() {
            if ($(window).width() < 768) {
                $('.sidebar .collapse').collapse('hide');
            };
        });

        // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
        $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
            if ($(window).width() > 768) {
                var e0 = e.originalEvent,
                    delta = e0.wheelDelta || -e0.detail;
                this.scrollTop += (delta < 0 ? 1 : -1) * 30;
                e.preventDefault();
            }
        });

        // Scroll to top button appear
        $(document).on('scroll', function() {
            var scrollDistance = $(this).scrollTop();
            if (scrollDistance > 100) {
                $('.scroll-to-top').fadeIn();
            } else {
                $('.scroll-to-top').fadeOut();
            }
        });

        // Smooth scrolling using jQuery easing
        $(document).on('click', 'a.scroll-to-top', function(e) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: ($($anchor.attr('href')).offset().top)
            }, 1000, 'easeInOutExpo');
            e.preventDefault();
        });

    })(jQuery); // End of use strict
</script>

<?php

use App\ClassroomModel;
use App\UserModel;
use App\PostModel;
use App\WorksheetModel;
use Carbon\Carbon;

?>

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" class="content py-2">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-0">Notifications</h3>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="text-primary font-weight-bold m-0">Urgent</h6>
                                </div>
                                <ul class="list-group list-group-flush">

                                    <?php
                                    foreach ($notifications as $notif) {
                                        $author = UserModel::where('id', '=', $notif->from)->first();
                                        if ($notif->seen == 0) {
                                            $s_class = " unseen";
                                        } else {
                                            $s_class = " seen";
                                        }

                                        $timediff = Carbon::parse($notif->created_at)->diffForHumans();
                                        ?>

                                        <li class="list-group-item {{ $s_class }}">
                                            <div class="row align-items-center no-gutters">
                                                <div class="col mr-2">
                                                    <h6 class="mb-0">
                                                        @if($notif->type == 1)
                                                        <?php $post = PostModel::where('id', '=', $notif->postid)->first(); ?>
                                                        {{$author->name}} posted a Question:
                                                        <a href="{{ route('notif_visit', [$notif->id]) }}">#{{$post->id}}</a>

                                                        @elseif($notif->type == 2)
                                                        <?php $ws = WorksheetModel::where('id', '=', $notif->postid)->first(); ?>
                                                        {{$author->name}} posted a Worksheet: 
                                                        <a href="{{ route('notif_visit', [$notif->id]) }}">#{{$ws->id}} "{{$ws->title}}"</a>

                                                        @elseif($notif->type == 3)
                                                        <?php $ws = WorksheetModel::where('id', '=', $notif->postid)->first(); ?>
                                                        {{$author->name}} invited you to atempt the worksheet: 
                                                        <a href="{{ route('notif_visit', [$notif->id]) }}">#{{$ws->id}} "{{$ws->title}}"</a>

                                                        @elseif($notif->type == 4)
                                                        <?php $class = ClassroomModel::where('id', '=', $notif->postid)->first(); ?>
                                                        {{$author->name}} invited you to Join the Classroom: 
                                                        <a href="{{ route('notif_visit', [$notif->id]) }}">#{{$class->id}} - "{{$class->name}}"</a>

                                                        @endif
                                                    </h6>
                                                    <span class="text-xs">{{ $timediff }}</span>
                                                </div>
                                            </div>
                                        </li>

                                    <?php } ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection