<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\DemoEmail;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function testsend()
    {
        $objDemo = new \stdClass();
        $objDemo->demo_one = 'Demo One Value';
        $objDemo->demo_two = 'Demo Two Value';
        $objDemo->sender = 'SenderUserName';
        $objDemo->receiver = 'ReceiverUserName';

        Mail::to("syednasim.work@gmail.com")->send(new DemoEmail($objDemo, "demo"));
    }

    public function adminmail(Request $request)
    {
        $user = UserModel::where("username", $request->username)->first();

        if ($request->mailtype == "demo") {
            $obj = new \stdClass();
            $obj->sender = 'CrowDoubt Admin';
            $obj->receiver = $user->name;

            Mail::to($user->email)->send(new DemoEmail($obj, "demo"));

            return redirect()->back();
        } else if ($request->mailtype == "welcome") {
            $obj = new \stdClass();
            $obj->sender = 'CrowDoubt Admin';
            $obj->receiver = $user->name;

            Mail::to($user->email)->send(new DemoEmail($obj, "welcome"));

            return redirect()->back();
        }
    }
}
