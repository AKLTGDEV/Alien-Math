@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<script>
    jQuery(document).ready(function() {
        $("title").text(`{{ $user->name }} {{ "(@".$user->username.")" }} / {{ config('app.name', 'Crowdoubt') }}`);

        /*$("#updatebtn").click(function(e) {
            $("#updateform").submit();
        })*/
    })
</script>

@include('logic.feeditem')

<?php

use App\users;
use Illuminate\Support\Facades\Auth;


$name = $user->name;
$username = $user->username;
$bio = users::getbio($username);
$tags = json_decode(users::gettags($user->username), true);
$nos_Q = $user->nos_Q;
$nos_A = $user->nos_A;
$nos_followers = $user->nos_followers;
$nos_following = $user->nos_following;
$rating = $user->rating;

$me = Auth::user();

if ($me->username == $username) {
    $self_flag = true;
} else {
    $self_flag = false;
}

$feeditem_ajax_url = route('getfeed_profile', [$username]);


?>

@include('profile.logic.getfeed')

<div class="cover"></div>
<div class="container content main">
    <div class="row justify-content-center">
        <div class="col-md" id="left">
            <div class="short-holder">
                @include('profile.short2')
            </div>
        </div>
        <div class="col-md">
            <div class="row mt-2">
                <!--<div class="col font-weight-bold"> Profile Info </div>-->
            </div>
            <div class="row mt-2">
                <div class="col-lg-6">
                    <a class="btn btn-sm btn-primary" href="{{ route('home') }}">
                        My Student Dashboard
                    </a>
                </div>
                <div class="col-lg-6">
                    <!-- TODO WIP PUT SOMETHING HERE -->
                </div>
            </div>
            <div class="row mt-1">
                <div class="col">
                    <div class="card">
                        <div class="card-body" id="feed_holder">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12" id="req">
                    <div style="width: 100%" class="mt-2 btn btn-outline-primary" id="req-text">
                        Load More
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection