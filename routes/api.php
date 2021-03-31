<?php

use App\payments;
use App\UserModel;
use App\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/rp-done', function (Request $request) {
    return payments::razorpay_premium_done($request);
})->name('rp-done');

Route::post('/rp-buy-ts/{encname}', function (Request $request, $encname) {
    //return payments::razorpay_premium_done($request);
    return payments::razorpay_ts_purchase($request, $encname);
})->name('rp-buy-ts');


Route::get('/test/{value}', 'RemoteController@test')->name('api-test');
// API Auth Endpoints
Route::post('login', 'Auth\LoginController@api_login')->name('api-login');
Route::post('register', 'Auth\RegisterController@api_register')->name('api-register');
Route::post('logout', 'Auth\LoginController@api_logout')->name('api-logout');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/admin-internal/class-auth/{username}/{cid}', 'RemoteController@int_class_auth')->name('api-int-class-auth');

    Route::post('check-uninitiated', 'RemoteController@check_new_user')->name('api-checknew');
    Route::post('getfeed', 'RemoteController@req_feed')->name('api-reqfeed');

    Route::post('/t/{tag}/basicinfo', 'TagsController@basicinfo')->name('api-tag-basicinfo');
    Route::post('/t/{tag}/gather', 'TagsController@gather')->name('api-namedtaggather');
    Route::post('/t/{tag}/follow', 'TagsController@follow_api')->name('api-namedtag-follow');
    Route::post('/t/{tag}/unfollow', 'TagsController@unfollow_api')->name('api-namedtag-unfollow');
    Route::post('/tags/top20', 'RemoteController@tags_top')->name('api-tagstop');

    Route::post('/useredit', 'RemoteController@profile_edit_validator')->name('api-usereditsubmit');
    Route::post('posts/answer', 'PostController@answer')->name('api-answerpost');
    Route::get('listclasses', 'RemoteController@listclasses')->name('api-listclasses');

    Route::post('/u/{uname}/getfeed', 'ProfileController@getfeed')->name('api-getfeed_profile');
    Route::post('/extinfo', function (Request $request) {
        $username = $request->username;
        $user = UserModel::where("username", $username)->first();
        if ($user != null) {

            $following_flag = false;
            if (Auth::user()->username != $username) {
                $self_following = json_decode(Auth::user()->following, true);
                if (in_array($user->id, $self_following)) {
                    $following_flag = true;
                } else {
                    $following_flag = false;
                }
            }

            return [
                "status" => "success",
                "username" => $username,
                "name" => $user->name,
                "rating" => $user->rating,
                "followers" => $user->nos_followers,
                "following" => $user->nos_following,
                "self_following" => $following_flag,
                "bio" => users::getbio($username),
                "tags" => users::gettags($username),
            ];
        } else {
            return [
                "status" => "error",
                "message" => "Username not found"
            ];
        }
    })->name('api-getfeed_profile');

    Route::post('/user/{uname}/follow', 'ProfileController@follow_api')->name('api-userfollow');
    Route::post('/user/{uname}/unfollow', 'ProfileController@unfollow_api')->name('api-userunfollow');


    Route::post('/posts/newsubmit', 'PostController@api_submit')->name('api-newpostsubmit');
    Route::post('/worksheets/newsubmit', 'WorksheetController@api_submit')->name('api-newwssubmit');

    Route::post('/notifs', 'NotificationsController@list_api')->name('api-notifs');

    Route::post('/class/basicinfo/{id}', 'ClassroomController@basicinfo')->name('api-class-basicinfo');
    Route::post('/class/join/{id}', 'ClassroomController@join_api')->name('api-joinclassroom');
    Route::post('/class/view/{id}/timeline-content', 'ClassroomController@get_timeline')->name('api-classroomtimeline_content');
    Route::post('/class/ans/q/{id}', 'ClassroomController@answerquestion')->name('api-class_ansq');
    Route::post('/class/post/note/{id}', 'RemoteController@classroom_postnote')->name('api-CLR_postnote');
    Route::post('/class/{id}/invite', 'ClassroomController@sendinvite_api')->name('api-classroomsendinvite');
    Route::post('/class/{id}/listmembers', 'ClassroomController@listmembers')->name('api-classroom-listmembers');

    Route::post('/ws/info/{slug}', 'RemoteController@publicws_getinfo')->name('api-ws-getinfo');
    Route::post('/ws/interim/{slug}', 'RemoteController@publicws_interim')->name('api-ws-interim');
    Route::post('/ws/pullcontent/{slug}', 'WorksheetController@pullcontent')->name('api-wsanswer-pc');
    Route::post('/ws/answer/s', 'WorksheetController@answer_submit')->name('api-answerws');
    Route::post('/ws/postanswer/{slug}', 'RemoteController@publicws_postanswer')->name('api-postanswerws');

    Route::post('/class/new', 'RemoteController@new_class')->name('api-newclass');
    Route::post('/class/post/q/{id}', 'ClassroomController@postquestion_api')->name('api-class-postquestion');
    Route::post('/class/post/ws/{id}', 'ClassroomController@postws_api')->name('api-class-postws');

    Route::post('/classws/info/{cid}/{encname}', 'RemoteController@classws_getinfo')->name('api-classws-getinfo');
    Route::post('/classws/interim/{cid}/{encname}', 'RemoteController@classws_interim')->name('api-classws-interim');
    Route::post('/classws/pullcontent/{cid}/{encname}', 'ClassroomController@pullcontentws')->name('api-class-wsanswer-pc');
    Route::post('/classws/sub', 'ClassroomController@answerwssub')->name('api-class_ws_answersub');
    Route::post('/classws/postanswer/{cid}/{encname}', 'RemoteController@classws_postanswer')->name('api-postanswerclassws');

    Route::post('/class/stats/{cid}/listws', 'ClassroomController@listws')->name('api-class-listws');
    Route::get('/class/{cid}/stats/{wsname}/att', 'ClassroomController@stats_attemptees')->name('api-class_stats_att');
    Route::get('/class/{cid}/stats/{wsname}/u/{uname}', 'ClassroomController@stats_userattempt')->name('api-class_stats_att_user');

    Route::post('/getQ/{id}', 'PostController@api_getinfo')->name('api-getq');
});
