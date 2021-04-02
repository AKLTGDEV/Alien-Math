@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><strong>{{ __($hits . " results gathered in " . $exec_time . " ms") }}</strong></div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @foreach($results as $res)

                    <div class="card mb-1">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <a class="text-primary" href='{{ route("question.". $res->table(), [$res->id]) }}'>
                                        @if($res->table() == "MCQ")
                                        {{ $res->id }}: {{ $res->getBody() }}
                                        @else
                                        {{ $res->id }}: {{ $res->digest }}
                                        @endif
                                    </a> <br>

                                    <span class="text-muted">
                                        {{ $res->topics }}

                                        <span class="badge badge-secondary">{{ $res->type }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection