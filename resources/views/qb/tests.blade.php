@extends('layouts.app')

@section('content')

@include('qb.loadtests')

<div class="container-fluid main">
    <div class="row mt-2">
        <div class="col-12">
            @include('qb.nav')
        </div>
    </div>

    <div class="row mt-2" id="posts_holder">
        @if($qlist_flag == true)
        <div class="feed_item col-sm-4">
            <div class="card feed_item">
                <div class="card-body">
                    <h3 class="text-bold">
                        Pending Test
                    </h3>
                    <p class="card-text" id="fi-body">
                        <ul>
                            <li>{{ count($qlist) }} Total questions</li>
                        </ul>
                    </p>
                </div>
                <div class="card-footer">
                    <div class="btn btn-sm btn-outline-primary">
                        Add more questions
                    </div>
                    <a class="btn btn-sm btn-outline-success" href="{{ route('qbank_tests_finalize') }}">
                        Finalize
                    </a>
                    <div class="btn btn-sm btn-outline-danger">
                        Scrap
                    </div>
                </div>
            </div>
        </div>
        @endif
        @foreach($tlist as $t)
        <div class="feed_item col-sm-4">
            <div class="card feed_item">
                <div class="card-body">
                    <h3 class="text-bold">
                        {{ $t['content']['title'] }}
                    </h3>
                    <p class="card-text" id="fi-body">
                        <ul>
                            <li>{{ $t['qnos'] }} Total questions</li>
                        </ul>
                    </p>
                </div>
                <div class="card-footer">
                    <a class="btn btn-sm btn-outline-success" href="{{ route('qbank_tests_getpdf', [$t['id']]) }}">
                        Get PDF
                    </a>
                    <div class="btn btn-sm btn-outline-danger">
                        Scrap
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row mt-2">
        <div class="col-md-8 offset-md-2" id="req">
            <div style="width: 100%" class="mt-2 btn btn-outline-primary" id="req-text">
                Load Tests
            </div>
        </div>
    </div>

</div>

@endsection