@extends('layouts.app')

@section('content')

<div class="container-fluid main">
    <div class="row mt-2">
        <div class="col-12">
            @include('qb.nav')
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <button type="button" data-toggle="modal" data-target="#NewTopicModal" class="btn btn-sm btn-outline-primary">
                Add Topic <span class="ml-1 fas fa-plus"></span>
            </button>
        </div>
    </div>

    <div class="row mt-2" id="topics_holder">
        @foreach($topics as $topic)
        <div class="col-md-4">
            <div class="card feed_item">
                <div class="card-body">
                    <h3 class="card-text text-bold">
                        {{ $topic->name }}
                    </h3>
                </div>
                <div class="card-footer">
                    <span class="badge badge-warning text-bold">Private</span>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('qbank_topic', [$topic->id]) }}">
                        Statistics
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>


<div class="modal fade" id="NewTopicModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Create New Topic
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('qbank_addtopic') }}" method="get">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="name" aria-label="name" aria-describedby="name" name="name" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection