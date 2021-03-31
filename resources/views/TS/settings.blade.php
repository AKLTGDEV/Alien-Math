@extends('layouts.app')

@section('content')

<div class="container-fluid main">
    <div class="row mt-2">
        <div class="col-md-6">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header">
                            General
                        </div>
                        <div class="card-body">
                            <h3 class="card-title text-center">
                                Test Series: {{ "$TS->name" }}
                            </h3>
                            <div class="card-text text-center mt-1">
                                <ul>
                                    <li>Set Price: <b>Rs. {{ $TS->amount }}</b></li>
                                    <li>Students: <b>{{ $nos_students }}</b></li>
                                    <li>Earnings so far: <b>Rs. {{ $earned_total }}</b> <div class="btn btn-sm btn-outline-secondary">Withdraw</div></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
        </div>
    </div>
</div>

@endsection