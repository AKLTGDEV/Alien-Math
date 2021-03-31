@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/newws.css') }}">

<div class="global-container">
    <div class="card login-form">
        <div class="card-body">

            <h3 class="card-title text-center">
                Test Series: "{{ $TS->name }}"
            </h3>
            <h4 class="text-center text-secondary">
                By <a href="{{ route('namedprofile', [$author->username]) }}">{{ "@".$author->username }}</a>
            </h4>
            <div class="card-text">
                <h5 class="text-center text-info">
                    Get now for only Rs. {{ $TS->amount }}
                </h5>

                <ul class="list-group mb-1">
                    <li class="list-group-item">{{ count(json_decode($info['wslist'], true)) }} Worksheets right now</li>
                    <li class="list-group-item">{{ count(json_decode($info['students'], true)) }} Students right now</li>
                    <!-- More items here -->
                </ul>

            </div>

            <form action="{{ route('rp-buy-ts', [$TS->encname]) }}" method="POST">
                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="rzp_test_oKaxXUBeTgWGT2" data-amount="{{ $order['amount'] }}" data-currency="INR" data-order_id="{{ $order['id'] }}" data-buttontext="Pay with Razorpay" data-name="CrowDoubt" data-description="Test transaction" data-button_theme="brand-color" data-image="{{ config('app.url') }}/favicon.png" data-prefill.name="{{ $user->name }}" data-prefill.email="{{ $user->email }}"></script>
                <input type="text" style="display: none" name="from" value="{{ $user->username }}">
            </form>

        </div>
    </div>
</div>

@endsection