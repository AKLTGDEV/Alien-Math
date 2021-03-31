@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">

                    @if($is_premium == 0)

                    <h3 class="card-title text-center">
                        You are not a Premium User
                    </h3>
                    <div class="card-text">
                        <h5 class="text-center text-info">
                            Get a premium membership now for only 150/Month.
                        </h5>

                        <ul class="list-group mb-1">
                            <li class="list-group-item">No Ads</li>
                            <li class="list-group-item">Dedicated user support</li>
                            <li class="list-group-item">Conduct Paid Classes</li>
                            <li class="list-group-item">Unlock Question Bank</li>
                        </ul>

                        <div class="text-center">
                            <form action="{{ route('rp-done') }}" method="POST">
                                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="rzp_live_UQM2wkHFTaxmee" data-amount="{{ $order['amount'] }}" data-currency="INR" data-order_id="{{ $order['id'] }}" data-buttontext="Pay with Razorpay" data-name="CrowDoubt" data-description="Buy CrowDoubt Premium" data-button_theme="brand-color" data-image="{{ config('app.url') }}/favicon.png" data-prefill.name="{{ $user->name }}" data-prefill.email="{{ $user->email }}"></script>
                                <input type="text" style="display: none" name="from" value="{{ $user->username }}">
                            </form>
                        </div>
                        <!--<div class="text-center">
                            <form>
                                <script src="https://checkout.razorpay.com/v1/payment-button.js" data-payment_button_id="pl_FkNuel1fS09eTl"> </script>
                            </form>
                        </div>-->

                    </div>

                    @else


                    <h3 class="card-title text-center text-success">
                        You are a Premium User!
                    </h3>
                    <div class="card-text text-center">
                        Your membership will end {{ $exp_date }}
                    </div>

                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@endsection