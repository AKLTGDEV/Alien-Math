@extends('layouts.app')

@section('content')

<style>
    .sidebar__inner {
        /*background: yellow;*/
    }

    .feed_item {
        margin-top: 2%;
        margin-bottom: 2%;
    }

    .btn {
        white-space: normal !important;
        word-wrap: break-word;
    }
</style>

<div class="row" style="height: 100%;">
    @if($carousel)
    @include('includes.topcarousel')
    @endif
</div>

<!--<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/sticky-sidebar.js"></script>-->

<script type="module">
    import StickySidebar from "./thirdparty/sticky-sidebar.js";

	var a = new StickySidebar('#sidebar', {
			topSpacing: 40,
			bottomSpacing: 20,
			containerSelector: '.container',
			innerWrapperSelector: '.sidebar__inner'
		});
</script>

<div style="padding: 5px;">
    <div class="container">
        <div class="row main" id="main-content">
            <div id="content" class="content feedholder col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
                <div class="container-fluid">

                    <div class="card">
                        <div class="card-body">
                            @include('logic.feeditem')
                            <?php
                            $story_id = 1;
                            foreach ($newsfeed as $nfitem) { ?>

                                @if ($nfitem['type'] == "POST")
                                <?php
                                    $post = $nfitem['body'];
                                    $corr = $post['correctopt'];
                                    $given = $post['givenopt'];
                                    $tags = json_decode($post['tags'], true);

                                    ?>
                                @include('includes.feeditem')

                                @else

                                <?php
                                    $ws = $nfitem['body'];
                                    $tags = json_decode($ws['tags'], true);
                                    ?>
                                @include('includes.wsitem')

                                @endif


                            <?php
                                $story_id++;
                            } ?>

                        </div>
                    </div>

                </div>
            </div>
            <div class="sidebar d-none d-xs-none d-sm-none d-md-block col-md-4 d-lg-block col-lg-4 d-xl-block col-xl-4">
                <div id="sidebar">
                    <div class="sidebar__inner">
                        <div class="container-fluid mt-3 mb-3">

                            @if ($tags_to_follow_flag)
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="card shadow">
                                        <div class="card-header py-3">
                                            <h6 class="text-primary font-weight-bold m-0">
                                                Trending Topics
                                            </h6>
                                        </div>
                                        <ul class="list-group list-group-flush">

                                            <?php foreach ($tags_to_follow as $ttf_name) { ?>
                                                <li class="list-group-item">
                                                    <div class="row align-items-center no-gutters">
                                                        <div class="col mr-2">
                                                            <h6 class="mb-0">
                                                                <strong>
                                                                    <a href="{{ route('namedtag', [$ttf_name]) }}">
                                                                        {{ $ttf_name }}
                                                                    </a>
                                                                </strong>
                                                            </h6>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a class="btn btn-outline-primary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('namedtagfollow', [$ttf_name]) }}">
                                                                &nbsp;Follow
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($people_to_follow_flag)
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="card shadow">
                                        <div class="card-header py-3">
                                            <h6 class="text-primary font-weight-bold m-0">
                                                People To Follow
                                            </h6>
                                        </div>
                                        <ul class="list-group list-group-flush">

                                            <?php foreach ($people_to_follow as $ptf_uname) { ?>
                                                <li class="list-group-item">
                                                    <div class="row align-items-center no-gutters">
                                                        <div class="col mr-2">
                                                            <h6 class="mb-0">
                                                                <strong>
                                                                    <a href="{{ route('namedprofile', [$ptf_uname]) }}">
                                                                        {{ "@".$ptf_uname }}
                                                                    </a>
                                                                </strong>
                                                            </h6>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a class="btn btn-outline-primary btn-sm d-none d-sm-inline-block" role="button" href="{{ route('userfollow', [$ptf_uname]) }}">
                                                                &nbsp;Follow
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($additional_pins_flag)
                            <div class="row">
                                <div class="col-12">
                                    nim deserunt id commodo. Veniam elit cillum, aute tail tri-tip venison qui in sed tongue in.
                                    Pork loin dolor consectetur adipisicing prosciutto id short ribs. Et chuck flank velit short
                                    ribs buffalo cupidatat rump strip steak tail cillum filet mignon landjaeger ipsum doner. Turkey magna cupim cow leberkas chislic labore chuck shank boudin. Tenderloin andouille venison tail boudin, short ribs beef burgdoggen picanha sirloin. Elit minim in ball tip tempor sint quis culpa.
                                    Aliqua venison qui sunt corned beef salami short loin elit sed short ribs cow eiusmod tempor.
                                    Rump do excepteur, bresaola porchetta ut beef. Pig short loin prosciutto cow, rump et velit.
                                    Aute pastrami ea, excepteur chislic brisket corned beef. Adipisicing cupim nostrud qui.
                                    Pork chop burgdoggen sunt brisket. Ipsum chuck porchetta nisi. T-bone in laborum strip steak
                                    labore cupim salami magna sint. Sunt biltong tongue jowl burgdoggen beef jerky. Tenderloin s
                                    hankle doner andouille e
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection