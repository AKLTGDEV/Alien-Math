<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\utils\similar_ws;
use App\worksheets;
use Illuminate\Http\Request;

Route::get('/', function () {

    if (Auth::check()) {
        // The user is logged in...
        return redirect()->route('home');
    } else {
        $wslist = [];
        foreach (similar_ws::get(0, 4) as $wsid) {
            array_push($wslist, worksheets::get($wsid));
        }
        return view('welcome-new', [
            "wslist" => $wslist,
        ]);
    }
});

Auth::routes(['verify' => true]);

Route::get('login/{driver}', 'Auth\LoginController@redirectToProvider')
    ->name('login.provider')
    ->where('driver', implode('|', config('auth.socialite.drivers')));

Route::get('login/{driver}/callback', 'Auth\LoginController@handleProviderCallback')
    ->name('login.callback')
    ->where('driver', implode('|', config('auth.socialite.drivers')));

Route::get('/social-connect/insta', 'ProfileEditController@connect_insta')->name('social.insta');

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/getfeed', 'HomeController@req_feed')->name('reqfeed');
Route::get('/explore', 'HomeController@explore')->name('explore');
Route::get('/premium-account', 'HomeController@premium_index')->name('premium_index');

Route::get('/qb', 'QBController@index')->name('qbank_index');
Route::post('/qb/list', 'QBController@questions')->name('qbank_listq');
Route::get('/qb/list-subtopics', 'QBController@list_subtopics')->name('qbank_list_subtopics');
Route::get('/qb/topics', 'QBController@index_topics')->name('qbank_index_topics');
Route::get('/qb/topics/new', 'QBController@addtopic')->name('qbank_addtopic');
Route::get('/qb/subtopics', 'QBController@index_subtopics')->name('qbank_index_subtopics');
Route::get('/qb/subtopics/new', 'QBController@addsubtopic')->name('qbank_addsubtopic');
Route::get('/qb/questions/new/mcq', 'QBController@newq_mcq')->name('qbank_newq_mcq');
Route::post('/qb/questions/new-submit/mcq', 'QBController@newq_mcq_validate')->name('qbank_newq_mcq_validate');
Route::get('/qb/questions/new/subjetive', 'QBController@newq_subjective')->name('qbank_newq_subjective');
Route::post('/qb/questions/new-submit/subjective', 'QBController@newq_subjective_validate')->name('qbank_newq_subjective_validate');
Route::get('/qb/topic/{id}', 'QBController@topic')->name('qbank_topic');
Route::post('/qb/list-topic/{id}', 'QBController@list_topic')->name('qbank_listq_topic');
Route::get('/qb/subtopic/{id}', 'QBController@subtopic')->name('qbank_subtopic');
Route::post('/qb/list-subtopic/{id}', 'QBController@list_subtopic')->name('qbank_listq_subtopic');
Route::get('/qb/tests', 'QBController@index_tests')->name('qbank_index_tests');
Route::post('/qb/tests/add-questions', 'QBController@tests_addq')->name('qbank_tests_addq');
Route::get('/qb/tests/pending/finalize', 'QBController@tests_finalize')->name('qbank_tests_finalize');
Route::post('/qb/tests/pending/finalize', 'QBController@tests_finalize_submit')->name('qbank_tests_finalize_submit');
Route::get('/qb/tests/pdf/{id}', 'QBController@tests_getpdf')->name('qbank_tests_getpdf')->middleware('premium');

Route::get('/posts/view/{id}', 'PostController@view')->name('viewpost');
Route::get('/posts/view/{id}/image', 'PostController@viewimage')->name('viewpostimage');
Route::get('/posts/new', 'PostController@new')->name('newpost');
Route::post('/posts/new', 'PostController@new_validate')->name('newpostsubmit');
Route::any('/posts/answer', 'PostController@answer')->name('answerpost');
Route::get('/posts/upload', 'PostController@upload')->name('uploadpost');
Route::post('/posts/upload', 'PostController@upload_validate')->name('uploadpost.validate');
Route::get('/posts/edit/{id}', 'PostController@edit')->name('editpost');
Route::post('/posts/edit/{id}', 'PostController@edit_submit')->name('editpost.submit');

Route::get('/saq/new', 'SAQController@new')->name('newsaq');
Route::post('/saq/new', 'SAQController@new_submit')->name('newsaqsubmit');
Route::get('/saq/upload', 'SAQController@upload')->name('uploadsaq');
Route::post('/saq/upload', 'SAQController@upload_validate')->name('uploadsaq.validate');
Route::get('/saq/edit/{id}', 'SAQController@edit')->name('editsaq');
Route::post('/saq/edit/{id}', 'SAQController@edit_submit')->name('editsaq.submit');


Route::get('/sqa/new', 'SQAController@new')->name('newsqa');
Route::post('/sqa/new', 'SQAController@new_submit')->name('newsqasubmit');
Route::get('/sqa/upload', 'SQAController@upload')->name('uploadsqa');
Route::post('/sqa/upload', 'SQAController@upload_validate')->name('uploadsqa.validate');
Route::get('/sqa/edit/{id}', 'SQAController@edit')->name('editsqa');
Route::post('/sqa/edit/{id}', 'SQAController@edit_submit')->name('editsqa.submit');


Route::get('/question/search', 'SearchController@question_search')->name('q.search');
Route::get('/question/mcq/{id}', 'QuestionController@view_mcq')->name('question.MCQ');
Route::get('/question/saq/{id}', 'QuestionController@view_saq')->name('question.SAQ');
Route::get('/question/sqa/{id}', 'QuestionController@view_sqa')->name('question.SQA');


Route::get('/user/{uname}/follow', 'ProfileController@follow')->name('userfollow');
Route::get('/user/{uname}/unfollow', 'ProfileController@unfollow')->name('userunfollow');
Route::get('/user/{uname}', function ($uname) {
    return redirect()->route('namedprofile', [$uname]);
});
Route::get('/u/{uname}', 'ProfileController@view')->name('namedprofile');
Route::get('/user/{uname}/profilepic', 'PublicController@profilepic');
Route::get('/useredit', 'ProfileEditController@view')->name('useredit');
Route::post('/useredit', 'ProfileEditController@validator')->name('usereditsubmit');
Route::post('/useredit-subimage', 'ProfileEditController@subimage')->name('useredit-subimage');
Route::get('/useredit/account', 'ProfileEditController@account')->name('useredit_acc');
Route::get('/usersetup', 'ProfileEditController@setup')->name('usersetup');
Route::get('/create-username', 'ProfileEditController@create_username')->name('createusername');
Route::post('/create-username', 'ProfileEditController@create_username_submit')->name('createusername_submit');
Route::post('/u/{uname}/getfeed', 'ProfileController@getfeed')->name('getfeed_profile');

Route::get('/tags/{tag}', function ($tagname) {
    return redirect()->route('namedtag', [$tagname]);
});
Route::get('/t/{tag}', 'TagsController@view')->name('namedtag');
Route::post('/t/{tag}/gather', 'TagsController@gather')->name('namedtaggather');
Route::get('/t/{tag}/follow', 'TagsController@follow')->name('namedtagfollow');
Route::get('/t/{tag}/unfollow', 'TagsController@unfollow')->name('namedtagunfollow');
Route::get('/t20', 'TagsController@top20')->name('tags_top_20');
Route::get('/newtopic', 'TagsController@request')->name('reqtopics');
Route::post('/newtopic', 'TagsController@request_submit')->name('reqtopics_sub');
Route::get('/newtopic/support/{id}', 'TagsController@request_support')->name('reqtopics_support');


Route::get('/worksheets/new', 'WorksheetController@create')->name('createworksheet');
Route::get('/worksheets/new/{nos}', 'WorksheetController@compose')->name('composeworksheet');
Route::post('/worksheets/newsubmit', 'WorksheetController@validator')->name('composews');
//Route::get('/worksheets/preanswer/{id}', 'WorksheetController@preanswer')->name('wsanswer-1');
Route::get('/worksheets/preanswer/{slug}', 'PublicController@wspreanswer')->name('wsanswer-1');
Route::get('/worksheets/answer/{slug}', 'WorksheetController@answer')->name('wsanswer-2');
Route::get('/worksheets/public-answer/{slug}', 'PublicController@ws_public_answer')->name('public-wsanswer-2');
Route::get('/worksheets/pullcontent/{slug}', 'WorksheetController@pullcontent')->name('wsanswer-pc');
Route::get('/worksheets/public-pullcontent/{slug}/{publicid}', 'PublicController@public_pullcontent')->name('public-wsanswer-pc');
Route::post('/worksheets/answer/s', 'WorksheetController@answer_submit')->name('answerws');
Route::post('/worksheets/answer/public-s/{publicid}', 'PublicController@public_answer_submit')->name('public-answerws');
Route::get('/worksheets/done/{slug}', 'WorksheetController@done')->name('wsanswer-3');
Route::get('/worksheets/public-done/{slug}/{publicid}', 'PublicController@wsdone')->name('public-wsanswer-3');
Route::get('/worksheets/remove/{id}', 'WorksheetController@delete')->name('wsdelete');
Route::get('/worksheets/edit', 'WorksheetController@edit')->name('wsedit');
Route::post('/worksheets/editsubmit/{wsname}', 'WorksheetController@editsubmit')->name('wsedit_submit');
Route::get('/worksheets/regattempt/{slug}/{publicid}', 'WorksheetController@reg_attempt')->name('register_public_attempt');
Route::get('/results/{shareid}', 'PublicController@publicresult')->name('wsresult');

Route::get('/stats', 'StatsController@view')->name('stats');
Route::get('/stats/{wsid}/att', 'StatsController@get_ws_attemptees')->name('stats_att_ws');
Route::get('/stats/{wsid}', 'StatsController@stats_ws')->name('stats_ws');
Route::get('/stats/{wsid}/{uname}', 'StatsController@stats_ws_user')->name('stats_ws_user');

Route::get('/notifs', 'NotificationsController@index')->name('notifs');
Route::get('/notif_view/{id}', 'NotificationsController@view')->name('notif_visit');

Route::group([
    'prefix' => 'class',
    'middleware' => ['premium']
], function () {

    Route::post('/join/{id}', 'ClassroomController@join')->name('joinclassroom');
    Route::get('/new', 'ClassroomController@new')->name('createclassroom');
    Route::post('/newsubmit', 'ClassroomController@validator')->name('createclassroomsubmit');
    Route::get('/view/{id}', 'ClassroomController@index')->name('viewclassroom');
    Route::get('/view/{id}/timeline', 'ClassroomController@timeline')->name('viewclassroomtimeline');
    Route::post('/view/{id}/timeline-content', 'ClassroomController@get_timeline')->name('classroomtimeline_content');
    //Route::get('/view/{id}/people', 'ClassroomController@people')->name('viewclassroompeople');
    Route::get('/view/{id}/stats', 'ClassroomController@stats')->name('class_stats');
    Route::get('/view/{id}/stream', 'ClassroomController@stream')->name('class_stream');
    Route::post('/view/{id}/invite', 'ClassroomController@sendinvite')->name('classroomsendinvite');
    Route::post('/post/note/{id}', 'ClassroomController@postnote')->name('CLR_postnote');
    Route::get('/post/q/{id}', 'ClassroomController@postquestion')->name('CLR_postq');
    Route::post('/post/q/{id}', 'ClassroomController@postquestion_validate')->name('CLR_postq_submit');
    Route::get('/post/ws/{id}', 'ClassroomController@postws')->name('CLR_postws');
    Route::post('/post/ws/{id}', 'ClassroomController@postws_validate')->name('CLR_postws_submit');
    Route::post('/ans/q/{id}', 'ClassroomController@answerquestion')->name('class_ansq');
    Route::get('/wspreanswer/{id}/{wsname}', 'ClassroomController@preanswerws')->name('class_ws_preanswer');
    Route::get('/wsanswer/{id}/{wsname}', 'ClassroomController@answerws')->name('class_ws_answer');
    Route::get('/wspull/{id}/{wsname}', 'ClassroomController@pullcontentws')->name('class_ws_answer_pullcontent');
    Route::get('/wspostanswer/{id}/{wsname}', 'ClassroomController@postanswerws')->name('class_ws_postanswer');
    Route::post('/wspostanswersub', 'ClassroomController@answerwssub')->name('class_ws_answersub');
    //Route::get('/{cid}/stats', 'ClassroomController@stats')->name('class_stats');
    Route::get('/{cid}/stats/{wsname}/att', 'ClassroomController@stats_attemptees')->name('class_stats_att');
    Route::get('/{cid}/stats/{wsname}/u/{uname}', 'ClassroomController@stats_userattempt')->name('class_stats_att_user');
    Route::get('/wspreview/{id}/{wsname}', 'ClassroomController@prevws')->name('class_ws_preview');
    Route::get('/qedit/{id}', 'ClassroomController@qedit')->name('class_q_edit');
    Route::get('/qremove/{cid}', 'ClassroomController@qremove')->name('class_q_remove');
    Route::get('/wsedit/{id}', 'ClassroomController@wsedit')->name('class_ws_edit');
    Route::get('/wsremove/{cid}', 'ClassroomController@wsremove')->name('class_ws_remove');
    Route::post('/wseditsubmit/{id}/{wsname}', 'ClassroomController@wseditsubmit')->name('class_ws_edit_submit');
    Route::get('/{cid}/ws-att-reset', 'ClassroomController@stats_reset')->name('class_ws_att_reset');
    Route::post('/docupload/{id}', 'ClassroomController@docupload')->name('class_docupload');
    Route::post('/jsonupload/{id}', 'ClassroomController@jsonupload')->name('class_ws_json');
    Route::get('/wsgetjson/{id}', 'ClassroomController@ws_getjson')->name('class_ws_getjson');
    Route::post('/rename/{id}', 'ClassroomController@rename')->name('class_rename');
    Route::post('/collections/new/{cid}', 'ClassroomController@newcollection')->name('class_newcollection');
    Route::get('/collections/view/{cid}/{encname}', 'ClassroomController@viewcollection')->name('class_viewcollection');
    Route::post('/collections/{cid}/{encname}/rename', 'ClassroomController@renamecollection')->name('class_coll_rename');
    Route::post('/collections/{cid}/{encname}/delete', 'ClassroomController@deletecollection')->name('class_coll_delete');
    Route::get('/{cid}/delete', 'ClassroomController@delete')->name('class_delete');
    Route::get('/user-remove/{cid}/{username}', 'ClassroomController@remove_user')->name('class_user_remove');
    Route::get('/user-remove-pending/{cid}/{username}', 'ClassroomController@remove_pendinguser')->name('class_user_removepending');
});


Route::get('/testseries/new', 'TSController@new')->name('createTS');
Route::post('/testseries/newsubmit', 'TSController@newsubmit')->name('createTSsub');
Route::get('/testseries/{encname}', 'TSController@index')->name('TSindex');
Route::get('/testseries/{encname}/compose', 'TSController@compose')->name('TScomposews');
Route::post('/testseries/{encname}/wssubmit', 'TSController@validator')->name('TScomposesubmit');
Route::get('/testseries/{encname}/preanswer/{wsname}', 'TSController@preanswer')->name('TSpreanswer');
Route::get('/testseries/{encname}/answer/{wsname}', 'TSController@answer')->name('TSanswer');
Route::post('/testseries/{encname}/submitanswer/{wsname}', 'TSController@answersub')->name('TSanswersub');
Route::get('/testseries/{encname}/pullcontent/{wsname}', 'TSController@pullcontent')->name('TSwspull');
Route::get('/testseries/{encname}/postanswer/{wsname}', 'TSController@postanswer')->name('TSpostanswer');
Route::get('/testseries/{encname}/postanswer/{wsname}', 'TSController@postanswer')->name('TSpostanswer');
Route::get('/testseries/{encname}/stats', 'TSController@stats')->name('TSstats');
Route::get('/testseries/{encname}/stats/{wsname}/att', 'TSController@stats_ws')->name('TSstats_ws');
Route::get('/testseries/{encname}/stats/{wsname}/u/{username}', 'TSController@stats_ws_u')->name('TSstats_ws_u');
Route::get('/testseries/{encname}/settings', 'TSController@settings')->name('TSsettings');


Route::get('/admin', 'AdminController@index')->name('admin');
Route::post('/admin', 'AdminController@work')->name('adminwork');
Route::get('/admin/composews', 'AdminController@composews')->name('admincomposews');
Route::post('/admin/subws', 'AdminController@ws_validator')->name('adminsubws');
Route::post('/admin/postws', 'AdminController@post_ws_as_user')->name('adminpostws');
Route::get('/admin/doc/{id}', 'AdminController@docindex')->name('admindocindex');
Route::get('/admin/docupload/{id}', 'AdminController@docupload')->name('admindocupload');
Route::get('/admin/docget/{id}', 'AdminController@docget')->name('admindocgetfile');
Route::post('/admin/docpost/{id}', 'AdminController@doc_validate')->name('docupload_submit');
Route::post('/admin/createuser', 'AdminController@createuser')->name('admincreateuser');
Route::post('/admin/prevws', 'AdminController@prevws')->name('adminpreviewws');
Route::post('/admin/loginuser', 'AdminController@loginas')->name('adminloginuser');
Route::get('/admin/mail', 'MailController@adminmail')->name('adminmail');
Route::get('/admin/posts/gen_slug', 'AdminController@post_genslug')->name('admin_post_slug');
Route::get('/admin/ws/gen_slug', 'AdminController@ws_genslug')->name('admin_ws_slug');
Route::get('/admin/posts/purge_slug', 'AdminController@post_purgeslug')->name('admin_purge_slug');
Route::get('/admin/ws/purge_slug', 'AdminController@ws_purgeslug')->name('admin_purge_ws_slug');
Route::get('/admin/jsonedit', 'AdminController@jsonedit')->name('admin_jsonedit');
Route::post('/admin/explodews', 'AdminController@exlpode_ws_as_user')->name('adminexplodews');


/*Route::get('/admin/testsend', 'MailController@testsend');
Route::get('admin/welcomeview', function (){
    return view("mails.welcome", [
        "searchbar" => true
    ]);
});*/

Route::get('/search', 'SearchController@index')->name('search');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
