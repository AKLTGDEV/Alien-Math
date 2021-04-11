@extends('layouts.app')
@section('content')
<style>
    #content {
        background: #fff;
        margin-top: 1%;
        margin-bottom: 1%;
        padding: 5px;
    }
</style>

<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/bs-charts.js"></script>
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/chart.min.js"></script>

@include('stats.logic.admin')

<div class="container-fluid main">
    <div class="row">
        <div class="col-12">
            <div id="content" id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-dark mb-0">Admins' Dashboard</h2>
                    </div>

                    <div class="row m-1">
                        <h4 class="text-secondary">Questions</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <canvas id="q_chart">
                            </canvas>
                        </div>
                    </div>
                    <div class="row m-2">
                        <a class="btn btn-sm btn-primary" href="{{ route('admin.adjust-difficulties') }}">
                            Adjust Difficulty
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection