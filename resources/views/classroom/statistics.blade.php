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
<script type="text/javascript" src="{{ config('app.url') }}/thirdparty/chart.min.js"></script>\




<script>
   jQuery(document).ready(function($) {
      var GLOBAL_UNAME = "";
      var GLOBAL_WSNAME = "";


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
         wsname = $(this).attr("wsname");
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
            url: "{{ config('APP_URL') }}/class/{{ $cid }}" + "/stats/" + wsname + "/att",
            method: 'get',
            data: {
               _token: CSRF_TOKEN
            },
            success: function(result) {
               result.forEach(att => {
                  console.log(att);
                  username = att['username'];
                  name = att['name'];
                  $("#attemptees-list").append('<a wsname="' + wsname + '" uname="' + username + '" class="useritem dropdown-item">' + name + '</a>');
               });
            }
         });
      });

      function wschart_update(type) {
         var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
         });
         $.ajax({
            url: "{{ config('APP_URL') }}/class/{{ $cid }}" + "/stats/" + GLOBAL_WSNAME + "/u/" + GLOBAL_UNAME,
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
         GLOBAL_WSNAME = $(this).attr("wsname");

         wschart_update("timetaken");
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
               "data": result.metrics[0]
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
                  <h3 class="text-dark mb-0">Dashboard - {{ $class->name }}</h3>
               </div>
               <div class="row">
                  <div class="col-md-6 col-xl-3 mb-4">
                     <div class="card shadow border-left-primary py-2">
                        <div class="card-body">
                           <div class="row align-items-center no-gutters">
                              <div class="col mr-2">
                                 <div class="text-uppercase text-primary font-weight-bold text-xs mb-1">
                                    <span>
                                       Questions/WS Posted
                                    </span>
                                 </div>
                                 <div class="text-dark font-weight-bold h5 mb-0">
                                    <span>{{$nos_q_ws}}</span>
                                 </div>
                              </div>
                              <div class="col-auto"><i class="fas fa-calendar fa-2x text-gray-300"></i></div>
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
                                       Questions/WS Answered
                                    </span>
                                 </div>
                                 <div class="text-dark font-weight-bold h5 mb-0">
                                    <span>{{ $nos_q_ws_ans }}</span>
                                 </div>
                              </div>
                              <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
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
                                    <span>Class Aggregate</span>
                                 </div>
                                 <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                       <div class="text-dark font-weight-bold h5 mb-0 mr-3"><span>20%</span></div>
                                    </div>
                                    <div class="col">
                                       <div class="progress progress-sm">
                                          <div class="progress-bar bg-info" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;"><span class="sr-only">20%</span></div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
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
                                    <span>Members</span>
                                 </div>
                                 <div class="text-dark font-weight-bold h5 mb-0">
                                    <span>20</span>
                                 </div>
                              </div>
                              <div class="col-auto"><i class="fas fa-comments fa-2x text-gray-300"></i></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!--ROW-->
               <div class="row">
                  <div class="col-12">
                     <div class="d-sm-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark mb-0">Worksheets</h3>
                        <a class="btn btn-primary btn-sm d-none d-sm-inline-block" role="button" href="#"><i class="fas fa-download fa-sm text-white-50"></i>&nbsp;Generate Report</a>
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
                           <a wsname=" {{$ws['name']}}" class="wsitem dropdown-item">{{ $ws['title'] }}</a>
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
                                       <a id="wsaction-timetaken" class="dropdown-item active" role="presentation">&nbsp;Time taken</a>
                                       <a id="wsaction-flicked" class="dropdown-item" role="presentation">&nbsp;Flicked</a>
                                       <div class="dropdown-divider"></div>
                                       <a class="dropdown-item" role="presentation" href="#">&nbsp;User Engagement</a>
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