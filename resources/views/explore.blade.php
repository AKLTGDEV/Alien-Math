@extends('layouts.app')

@section('content')

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-2 py-1">
                        <h3 class="text-dark mb-0">Explore</h3>
                    </div>

                    <?php foreach ($internals as $int_acc) { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="text-primary font-weight-bold m-0">
                                            From <a href="{{ route('namedprofile', [$int_acc]) }}">{{ "@".$int_acc }}</a>
                                        </h6>
                                    </div>

                                    <div class="card-body">
                                        <div class="container-fluid">
                                            <?php
                                                $internal = $material[$int_acc];
                                                ?>
                                            <div class="row">

                                                <style>
                                                    .item {
                                                        min-width: 50%;
                                                    }
                                                </style>

                                                <?php foreach ($internal as $ws) {
                                                        $tags = json_decode($ws['tags'], true);
                                                        $ws['mine'] = false;
                                                        ?>

                                                    <div class="col-md item">
                                                        @include('includes.wsitem')
                                                    </div>

                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection