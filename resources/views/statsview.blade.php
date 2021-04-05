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

<script>
   jQuery(document).ready(function($) {
      var GLOBAL_UNAME = "";
      var GLOBAL_WSID = "";
      var tags_ask = JSON.parse('<?php
                                 if (count($tags_posted) > 4) {
                                    echo json_encode(array_slice($tags_posted, 0, 4));
                                 } else {
                                    echo json_encode($tags_posted);
                                 }
                                 ?>');

      var tags_ans = JSON.parse('<?php
                                 if (count($tags_answered) > 4) {
                                    echo json_encode(array_slice($tags_answered, 0, 4));
                                 } else {
                                    echo json_encode($tags_answered);
                                 }
                                 ?>');


      var dr = JSON.parse('<?php
                           echo json_encode($daily_record);
                           ?>');
      var dr_ctx = document.getElementById('dr_chart').getContext('2d');
      var dr_chart = new Chart(dr_ctx, {
         "type": "line",
         "data": {
            "labels": Object.keys(dr),
            "datasets": [{
               "label": "Rating",

               "data": Object.values(dr)
            }]
         },
         "options": {
            "maintainAspectRatio": false,
            "legend": {
               "display": false
            },
            "title": {}
         }
      })




      var ctx = document.getElementById('topics-chart').getContext('2d');
      var topicschart = new Chart(ctx, {
         "type": "doughnut",
         "data": {},
         "options": {
            "maintainAspectRatio": false,
            "legend": {
               "display": false
            },
            "title": {}
         }
      })

      update_topics_chart("posted");

      var ctx2 = document.getElementById('ws-user-chart').getContext('2d');
      var ws_user_chart = new Chart(ctx2, {
         "type": "bar",
         "data": {},
         "options": {
            "maintainAspectRatio": false,
            "legend": {
               "display": false
            },
            "title": {}
         }
      })

      $(".wsitem").click(function(e) {
         wsid = $(this).attr("wsid");
         $("#dropdownMenuButtonWorksheets").text($(this).text())
         $("#dropdownMenuButtonAttemptees").prop("disabled", false);

         //We have the wsid. Get the list of attemptees from server and populate the entries.
         $("#attemptees-list").empty();
         var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
         });
         $.ajax({
            url: "{{ config('APP_URL') }}/stats/" + wsid + "/att",
            method: 'get',
            data: {
               _token: CSRF_TOKEN
            },
            success: function(result) {
               result.forEach(att => {
                  username = att[0];
                  name = att[1];
                  $("#attemptees-list").append('<a wsid="' + wsid + '" uname="' + username + '" class="useritem dropdown-item">' + name + '</a>');
               });
            }
         });
      });

      function wschart_update(type) {
         var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
         $("#dropdownMenuButtonAttemptees").text(GLOBAL_UNAME);
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
         });
         $.ajax({
            url: "{{ config('APP_URL') }}/stats/" + GLOBAL_WSID + "/" + GLOBAL_UNAME,
            method: 'get',
            data: {
               _token: CSRF_TOKEN
            },
            success: function(result) {
               if (type == "timetaken") {
                  ws_time_taken(result);
               } else {
                  ws_flicked(result);
               }
            }
         });
      }

      $("#attemptees-list").on('click', '.useritem', function(e) {
         GLOBAL_UNAME = $(this).attr("uname");
         GLOBAL_WSID = $(this).attr("wsid");

         wschart_update("timetaken");
      })

      $("#topics-posted").click(function(e) {
         $("#topics-answered").removeClass("active");
         $("#topics-posted").addClass("active");

         update_topics_chart("posted");
      })

      $("#topics-answered").click(function(e) {
         $("#topics-posted").removeClass("active");
         $("#topics-answered").addClass("active");

         update_topics_chart("answered");
      })


      $("#wsaction-timetaken").click(function(e) {
         $("#wsaction-flicked").removeClass("active");
         $("#wsaction-timetaken").addClass("active");

         wschart_update("timetaken")
      })

      $("#wsaction-flicked").click(function(e) {
         $("#wsaction-timetaken").removeClass("active");
         $("#wsaction-flicked").addClass("active");

         wschart_update("flicked")
      })

      function rand_color(size) {
         ret = [];
         for (let j = 0; j < size; j++) {
            var letters = "0123456789ABCDEF";
            var color = '#';
            for (var i = 0; i < 6; i++)
               color += letters[(Math.floor(Math.random() * 16))];

            ret.push(color);
         }

         return ret;
      }

      function update_topics_chart(type) {
         if (type == "posted") {
            $("#topics-heading").text("Topics - Posted")
            //console.log(tags_ask);

            topicschart.data = {
               "labels": Object.keys(tags_ask),
               "datasets": [{
                  "label": "",
                  "backgroundColor": rand_color(Object.keys(tags_ask).length),
                  "data": Object.values(tags_ask)
               }]
            };
            topicschart.update();

         } else {
            $("#topics-heading").text("Topics - Answered")
            //console.log(tags_ans);

            topicschart.data = {
               "labels": Object.keys(tags_ans),
               "datasets": [{
                  "label": "",
                  "backgroundColor": rand_color(Object.keys(tags_ans).length),
                  "data": Object.values(tags_ans)
               }]
            };

            topicschart.update();
         }
      }

      function ws_time_taken(result) {
         $("#ws-chart-title").text("Overview - Time Taken");

         var labels = [];
         var rightwrong = [];
         var netq = result.general.right + result.general.wrong + result.general.left;

         for (let k = 1; k <= netq; k++) {
            labels.push("Q" + k);

            if (result.results[k - 1] == "F") {
               rightwrong.push("#fc6203")
            } else if (result.results[k - 1] == "T") {
               rightwrong.push("#05f77e")
            } else {
               rightwrong.push("#a6aba2")
            }
         }

         ws_user_chart.data = {
            "labels": labels,
            "datasets": [{
               "label": "",
               "backgroundColor": rightwrong,
               "data": result.metrics.clock_hits
            }]
         };
         ws_user_chart.update();
      }

      function ws_flicked(result) {
         $("#ws-chart-title").text("Overview - Flicked");

         var labels = [];
         var rightwrong = [];
         var netq = result.general.right + result.general.wrong + result.general.left;

         for (let k = 1; k <= netq; k++) {
            labels.push("Q" + k);

            if (result.results[k - 1] == "F") {
               rightwrong.push("#fc6203")
            } else if (result.results[k - 1] == "T") {
               rightwrong.push("#05f77e")
            } else {
               rightwrong.push("#a6aba2")
            }
         }

         ws_user_chart.data = {
            "labels": labels,
            "datasets": [{
               "label": "",
               "backgroundColor": rightwrong,
               "data": result.metrics[1]
            }]
         };

         console.log(result.metrics[1])

         ws_user_chart.update();
      }
   })
</script>
<div class="container-fluid main">
   <div class="row">
      <div class="col-12">
         <div id="content" id="content">
            <div class="container-fluid">
               <div class="d-sm-flex justify-content-between align-items-center mb-4">
                  <h3 class="text-dark mb-0">Dashboard</h3>
               </div>
               <div class="row">
                  <div class="col-md-6 col-xl-3 mb-4">
                     <div class="card shadow border-left-primary py-2">
                        <div class="card-body">
                           <div class="row align-items-center no-gutters">
                              <div class="col mr-2">
                                 <div class="text-uppercase text-primary font-weight-bold text-xs mb-1">
                                    <span>
                                       Questions Posted
                                    </span>
                                 </div>
                                 <div class="text-dark font-weight-bold h5 mb-0">
                                    <span>{{$posts}}</span>
                                 </div>
                              </div>

                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6 col-xl-3 mb-4">
                     <div class="card shadow border-left-success py-2">
                        <div class="card-body">
                           <div class="row align-items-center no-gutters">
                              <div class="col mr-2">
                                 <div class="text-uppercase text-success font-weight-bold text-xs mb-1">
                                    <span>
                                       Questions Answered
                                    </span>
                                 </div>
                                 <div class="text-dark font-weight-bold h5 mb-0">
                                    <span>{{$answers}}</span>
                                 </div>
                              </div>

                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6 col-xl-3 mb-4">
                     <div class="card shadow border-left-info py-2">
                        <div class="card-body">
                           <div class="row align-items-center no-gutters">
                              <div class="col mr-2">
                                 <div class="text-uppercase text-info font-weight-bold text-xs mb-1">
                                    <span>Aggregate</span>
                                 </div>
                                 <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                       <div class="text-dark font-weight-bold h5 mb-0 mr-3"><span>{{$aggregate}}%</span></div>
                                    </div>
                                    <div class="col">
                                       <div class="progress progress-sm">
                                          <div class="progress-bar bg-info" aria-valuenow="{{$aggregate}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$aggregate}}%;"><span class="sr-only">{{$aggregate}}%</span></div>
                                       </div>
                                    </div>
                                 </div>
                              </div>

                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6 col-xl-3 mb-4">
                     <div class="card shadow border-left-warning py-2">
                        <div class="card-body">
                           <div class="row align-items-center no-gutters">
                              <div class="col mr-2">
                                 <div class="text-uppercase text-warning font-weight-bold text-xs mb-1">
                                    <span>Rating</span>
                                 </div>
                                 <div class="text-dark font-weight-bold h5 mb-0">
                                    <span>{{$rating}}</span>
                                 </div>
                              </div>

                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-lg-7 col-xl-8">
                     <div class="card shadow mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                           <h6 class="text-primary font-weight-bold m-0">Overview</h6>
                           <!--<div class="dropdown no-arrow">
                              <button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v text-gray-400"></i></button>
                              <div class="dropdown-menu shadow dropdown-menu-right animated--fade-in" role="menu">
                                 <p class="text-center dropdown-header">Stats for:</p>
                                 <a class="dropdown-item" role="presentation" href="#">&nbsp;Questions/WS posted</a>
                                 <a class="dropdown-item" role="presentation" href="#">&nbsp;Questions/WS Answered</a>
                              </div>
                           </div>-->
                        </div>
                        <div class="card-body">
                           <div class="chart-area">
                              <canvas id="dr_chart">
                              </canvas>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-5 col-xl-4">
                     <div class="card shadow mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                           <h6 id="topics-heading" class="text-primary font-weight-bold m-0">Topics</h6>
                           <div class="dropdown no-arrow">
                              <button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v text-gray-400"></i></button>
                              <div class="dropdown-menu shadow dropdown-menu-right animated--fade-in" role="menu">
                                 <p class="text-center dropdown-header">Stats for:</p>
                                 <a href="#" id="topics-posted" class="dropdown-item active" role="presentation">&nbsp;Posted</a>
                                 <a href="#" id="topics-answered" class="dropdown-item" role="presentation">&nbsp;Answered</a>
                              </div>
                           </div>
                        </div>
                        <div class="card-body">
                           <div class="chart-area">
                              <canvas id="topics-chart">

                              </canvas>
                           </div>
                           <!--<div class="text-center small mt-4">
                              <span class="mr-2"><i class="fas fa-circle text-primary"></i>&nbsp;Direct</span>
                              <span class="mr-2"><i class="fas fa-circle text-success"></i>&nbsp;Social</span>
                              <span class="mr-2"><i class="fas fa-circle text-info"></i>&nbsp;Refferal</span>
                           </div>-->
                        </div>
                     </div>
                  </div>
               </div>
               <!--ROW-->
               <div class="row">
                  <div class="col-12">
                     <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-0">All Worksheets</h3>
                     </div>
                  </div>
               </div>
               <div class="row m-1">
                  <div class="dropdown m-1">
                     <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonWorksheets" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php if (count($worksheets) == 0) echo "disabled"; ?>>
                        Worksheets
                     </button>
                     <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonWorksheets">
                        <?php foreach ($worksheets as $ws) { ?>
                           <a wsid=" {{$ws[0]}}" class="wsitem dropdown-item">{{ $ws[1] }}</a>
                        <?php } ?>
                     </div>
                  </div>
                  <div class="dropdown m-1">
                     <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButtonAttemptees" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled>
                        Attemptee
                     </button>
                     <div class="dropdown-menu" id="attemptees-list" aria-labelledby="dropdownMenuButtonAttemptees">
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-12 mt-4">
                     <div class="row">
                        <div class="col-md-12">
                           <div class="card shadow mb-4">
                              <div class="card-header d-flex justify-content-between align-items-center">
                                 <h6 id="ws-chart-title" class="text-primary font-weight-bold m-0">Overview</h6>
                                 <div class="dropdown no-arrow">
                                    <button class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button"><i class="fas fa-ellipsis-v text-gray-400"></i></button>
                                    <div class="dropdown-menu shadow dropdown-menu-right animated--fade-in" role="menu">
                                       <p class="text-center dropdown-header">Stat Type</p>
                                       <div id="wsaction-timetaken" class="dropdown-item active" role="presentation">&nbsp;Time taken</div>
                                       <div id="wsaction-flicked" class="dropdown-item" role="presentation">&nbsp;Flicked</div>
                                    </div>
                                 </div>
                              </div>
                              <div class="card-body">
                                 <div class="chart-area">
                                    <canvas id="ws-user-chart"></canvas>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   @endsection