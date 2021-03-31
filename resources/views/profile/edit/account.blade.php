@extends('layouts.app')

@section('content')

<?php

use App\users;
use Illuminate\Support\Facades\Auth;

?>

<script>
    jQuery(document).ready(function() {
        //
    })
</script>


<div class="global-container">
    <div class="row mt-2">
        <div class="col-12 col-md-8 ml-md-auto mr-md-auto">
            <div class="card">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('useredit') }}">Basic Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('useredit_acc') }}">Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Billing</a>
                    </li>
                </ul>

                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="lead">
                                <h3 class="text-secondary">
                                    Social Media Profiles
                                </h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@endsection