<?php

namespace App;

include 'razorpay/Razorpay.php';

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;
use Razorpay\Api\Api;
use App\UserModel;

class payments
{

    public static function razorpay_order($amount)
    {
        /*$api = new Api(
            'rzp_test_oKaxXUBeTgWGT2', // API key
            'FvYiJicaANzy5hnZdguf5g7z' // Secret
        ); THESE ARE TEST MODE DETAILS */

        $api = new Api(
            'rzp_live_UQM2wkHFTaxmee', // API key
            '5SPcGvxePqXgkYiBO0Q3SzLH' // Secret
        );

        /**
         * NOTE: In case of altercation of API key, change is to be made on blade template accordingly
         * (data-key attribute of the razorpay script)
         */

        $order = $api->order->create(
            array(
                'receipt' => '123',
                'amount' => ($amount * 100),
                'currency' => 'INR'
            )
        );

        return $order;
    }

    public static function razorpay_premium_done(Request $request)
    {
        // Store the data in the 'payment' table

        $payment = new PaymentsModel;

        $payment->type = "PREMIUM";
        $payment->amount = "150";
        $payment->from = $request->from;
        $payment->data = json_encode($request->all(), true);

        $payment->save();

        return redirect()->route('premium_index');
    }

    public static function razorpay_ts_purchase(Request $request, $encname)
    {
        $TS = TSModel::where("encname", $encname)->first();

        //Add current student to students' list
        $info_items = json_decode(Storage::get("TS/$encname/info.json"), true);
        $students = json_decode($info_items['students'], true);
        array_push($students, $request->from);
        $info_items['students'] = json_encode($students, true);
        Storage::put("TS/$encname/info.json", json_encode($info_items));

        $buyer = UserModel::where("username", $request->from)->first();
        $ts_bought = json_decode($buyer->ts_bought, true);
        array_push($ts_bought, $TS->id);
        $buyer->ts_bought = json_encode($ts_bought, true);
        $buyer->save();

        // Store the data in the 'payment' table
        $payment = new PaymentsModel;

        $payment->type = "TS";
        $payment->amount = $TS->amount;
        $payment->from = $request->from;
        $payment->data = json_encode($request->all(), true);

        $payment->save();

        return redirect()->route('TSindex', [$TS->encname]);
    }
}
