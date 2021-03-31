@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/feeditem_post.css') }}">

@include('logic.feeditem')

<link rel="stylesheet" href="{{ asset('css/viewpost.css') }}">

<div class="container-fluid main">
    <div class="row" style="margin-top: 2%">
        <div class="offset-md-1 col-md-10 col-sm-12 col-xs-12">
            <?php
            $corr = $post['correctopt'];
            $given = $post['givenopt'];
            $tags = json_decode($post['tags'], true);
            ?>
            @include('includes.feeditem')
        </div>
    </div>
</div>

@endsection