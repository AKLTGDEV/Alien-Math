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


<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-bs4.min.js"></script>

<script>
    $(document).ready(function() {
        $("#post_note").summernote();
        inputNumber($('.input-number'));

        /**
         * Prepare Q answer submission
         *
         */

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        jQuery('.class_q_option').click(function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            __el = $(this)
            qname = $(this).attr("unq_name")
            __opt = $(this).attr("opt")

            //console.log(qname + " " + __opt);

            $.ajax({
                url: "{{ route('class_ansq', [$class->id]) }}",
                method: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    qname: qname,
                    given: __opt
                },
                success: function(result) {

                    console.log(result);

                    //Color the option box accordingly
                    if (result == "SUCCESS") {
                        __el.removeClass("btn-outline-primary btn-rounded")
                        __el.addClass("btn-success")
                    } else if (result == "FAILURE") {
                        __el.removeClass("btn-outline-primary btn-rounded")
                        __el.addClass("btn-danger")
                    }
                }
            });
        });
    })

    function inputNumber(el) {
        var min = el.attr('min') || false;
        var max = el.attr('max') || false;

        var els = {};

        els.dec = el.prev();
        els.inc = el.next();

        el.each(function() {
            init($(this));
        });

        function init(el) {

            els.dec.on('click', decrement);
            els.inc.on('click', increment);

            function decrement() {
                var value = el[0].value;
                value--;
                if (!min || value >= min) {
                    el[0].value = value;
                }
            }

            function increment() {
                var value = el[0].value;
                value++;
                if (!max || value <= max) {
                    el[0].value = value++;
                }
            }
        }
    }
</script>

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
                    <div class="card feed_item">
                        <form id="note_f" action="{{ route('CLR_postnote', [$class->id]) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-header">
                                <button class="btn btn-primary" type="submit">
                                    Post Note
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <textarea id="post_note" name="note" style="width: 100%"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card feed_item">
                        <div class="card-body">
                            @if (count($actilog) == 0)

                            No posts on The board yet.

                            @else

                            <?php foreach ($actilog as $actilog_item) {
                                $author = $actilog_item['author'];
                                $object = $actilog_item['content'];
                                $unique_name = $actilog_item['name'];
                                ?>


                                @if($actilog_item['type'] == 1)
                                <?php $note_body = $object['body']; ?>
                                <div class="card actilog_item_note shadow mt-2 mb-2">
                                    <div class="card-body">
                                        <div class="card-title d-sm-flex justify-content-between align-items-center">
                                            <span>
                                                <a class="avatar mx-auto white" href="{{ config('app.url') }}/u/{{ $author->username }}">
                                                    <img style="height: 40px;" src="{{ config('app.url') }}/user/{{ $author->username }}/profilepic" alt="avatar mx-auto white" class="rounded-circle img-fluid">
                                                    <span>
                                                        {{ $author->name }}
                                                    </span>
                                                </a>
                                            </span>
                                            <div class="text-secondary d-none d-sm-inline-block">
                                                {{ $actilog_item['samay'] }}
                                            </div>
                                        </div>
                                        <p class="card-text">
                                            <?php echo $note_body; ?>
                                        </p>
                                    </div>
                                </div>


                                @elseif($actilog_item['type'] == 2)
                                <?php
                                    $q_body = $object['body'];
                                    $corr = $object['correct'];
                                    $given = $actilog_item['given'];
                                    $opts_object = json_decode($object['opts'], true);

                                    ?>
                                <div class="card actilog_item_question shadow mt-2 mb-2">
                                    <div class="card-body">
                                        <div class="card-title d-sm-flex justify-content-between align-items-center">
                                            <span>
                                                @if ($object['title'] != null)
                                                <h4>{{ $object['title'] }}</h4>
                                                @endif
                                                <a class="avatar mx-auto white" href="{{ config('app.url') }}/u/{{ $author->username }}">
                                                    <img style="height: 40px;" src="{{ config('app.url') }}/user/{{ $author->username }}/profilepic" alt="avatar mx-auto white" class="rounded-circle img-fluid">
                                                    <span>
                                                        {{ $author->name }}
                                                    </span>
                                                </a>
                                                <div class="text-secondary d-none d-sm-inline-block">
                                                    {{ $actilog_item['samay'] }}
                                                </div>
                                            </span>
                                        </div>
                                        <p class="card-text">
                                            <?php echo $q_body; ?>
                                        </p>
                                        <div class="section-fluid">
                                            <div class="post-tags-list">
                                                <?php foreach ($object['tags'] as $tag) { ?>
                                                    <a style="text-decoration:none" class="badge badge-secondary" href="{{ config('app.url') }}/tags/{{ $tag }}">{{ $tag }}</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="section-fluid mt-1">
                                            @include('classroom.includes.q-opts')
                                        </div>
                                    </div>
                                </div>



                                @elseif($actilog_item['type'] == 3)
                                <div class="card actilog_item_ws shadow mt-2 mb-2">
                                    <div class="card-body">
                                        <div class="card-title d-sm-flex justify-content-between align-items-center">
                                            <span>
                                                @if ($object['title'] != null)
                                                <h4>{{ $object['title'] }}</h4>
                                                @endif
                                                <a class="avatar mx-auto white" href="{{ config('app.url') }}/u/{{ $author->username }}">
                                                    <img style="height: 40px;" src="{{ config('app.url') }}/user/{{ $author->username }}/profilepic" alt="avatar mx-auto white" class="rounded-circle img-fluid">
                                                    <span>
                                                        {{ $author->name }}
                                                    </span>
                                                </a>
                                                <div class="text-secondary d-none d-sm-inline-block">
                                                    {{ $actilog_item['samay'] }}
                                                </div>
                                            </span>
                                        </div>
                                        <p class="card-text">
                                            <ul>
                                                <li>{{ $object['nos'] }} Questions</li>
                                                <li>To be completed in {{ $object['time'] }} Minutes</li>
                                            </ul>
                                        </p>
                                        <div class="section-fluid">
                                            <div class="ws-tags-list">
                                                <?php foreach ($object['tags'] as $tag) { ?>
                                                    <a style="text-decoration:none" class="badge badge-secondary" href="{{ config('app.url') }}/tags/{{ $tag }}">{{ $tag }}</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="section-fluid">
                                            <a href="{{ route('class_ws_preanswer', [$class->id, $actilog_item['name']]) }}" class="btn btn-outline-info btn-sm shadow mt-1">
                                                @if ( $actilog_item['ws_attempted'] )
                                                Attempted
                                                @else
                                                Attempt
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                @endif


                            <?php } ?>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar d-none d-xs-none d-sm-none d-md-block col-md-4 d-lg-block col-lg-4 d-xl-block col-xl-4">
                <div id="sidebar">
                    <div class="sidebar__inner">
                        <div class="container-fluid mt-3">
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="card shadow">
                                        <div class="card-header py-3">
                                            <h6 class="text-primary font-weight-bold m-0">
                                                Classroom
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <h4 class="card-title text-center">
                                                {{ $class->name }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 mt-2">
                                    <div class="card shadow">
                                        <div class="card-header py-3">
                                            <h6 class="text-primary font-weight-bold m-0">
                                                Me
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            Information about user
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($isadmin)
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="btn-group shadow" role="group" aria-label="Controls">
                                            <a href="{{ route('CLR_postq', [$class->id]) }}">
                                                <button type="button" class="btn btn-outline-primary btn-rounded waves-effect">
                                                    Post Question
                                                </button>
                                            </a>
                                            <button type="button" class="btn btn-outline-primary btn-rounded waves-effect" data-toggle="modal" data-target="#postwsModal">
                                                Post Worksheet
                                            </button>
                                            <a href="{{ route('class_stats', [$class->id]) }}">
                                                <button style="height: 100%" type="button" class="btn btn-outline-primary btn-rounded waves-effect">
                                                    Statistics
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if ($extra_pins_flag)
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

<style>
    .input-number {
        width: 80px;
        padding: 0 12px;
        vertical-align: top;
        text-align: center;
        outline: none;
    }

    .input-number,
    .input-number-decrement,
    .input-number-increment {
        border: 1px solid #ccc;
        height: 40px;
        user-select: none;
    }

    .input-number-decrement,
    .input-number-increment {
        display: inline-block;
        width: 30px;
        line-height: 38px;
        background: #f1f1f1;
        color: #444;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
    }

    .input-number-decrement:active,
    .input-number-increment:active {
        background: #ddd;
    }

    .input-number-decrement {
        border-right: none;
        border-radius: 4px 0 0 4px;
    }

    .input-number-increment {
        border-left: none;
        border-radius: 0 4px 4px 0;
    }
</style>

<div class="modal" tabindex="-1" role="dialog" id="postwsModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('CLR_postws', [$class->id]) }}" method="get" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Post Worksheet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Number of questions
                        </label>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="input-number-decrement">–</span><input id="nos_Q" class="input-number" type="text" value="1" min="0" max="50" name="nos"><span class="input-number-increment">+</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Proceed</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection