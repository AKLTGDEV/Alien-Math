@extends('layouts.app')

@section('content')

<!-- Class NAV -->
<nav class="navbar navbar-expand navbar-light bg-light">
    <div class="d-flex">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="navbar-collapse collapse justify-content-center" id="collapsingNavbar">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('viewclassroom', [$class->id]) }}">Class</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('viewclassroomtimeline', [$class->id]) }}">Timeline</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('class_stats', [$class->id]) }}">Stats</a>
            </li>
        </ul>
    </div>
</nav>

<script>
    $(document).ready(function() {
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
</script>


<div class="container-fluid">
    <div class="row">
        <div class="offset-md-1 col-md-10">
            <style>
                .jumbotron {
                    color: white;
                    background-image: url('{{ config('app.url') }}/images/bg4.png');
                    height: 50vh;
                }
            </style>
            <div class="jumbotron jumbotron-fluid mt-1 shadow">
                <div class="container">
                    <h1 class="display-4">
                        <b>{{ $class->name }}</b>
                    </h1>
                    <p>
                        {{ $class->users }} Members <br>
                        #{{ substr($class->encname, 0, 6) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="offset-md-1 col-md-10">
        <div class="row">
            <div class="col-sm-4">
                @include('classroom.includes.pending')
            </div>
            <div class="col-sm-8">

                <div class="row">
                    <div class="col-12">
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
                                                @if ( $actilog_item['ws_attempted'] || $isadmin == true )
                                                <a href="{{ route('class_ws_preview', [$class->id, $actilog_item['name']]) }}" class="btn btn-outline-info btn-sm shadow mt-1">
                                                    Preview
                                                </a>
                                                @endif
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

            </div>
        </div>
    </div>
</div>

@endsection