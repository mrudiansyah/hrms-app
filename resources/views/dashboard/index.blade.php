@extends('layouts/admin')
@section('Contents')
<meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    canvas {
      -moz-user-select: none;
      -webkit-user-select: none;
      -ms-user-select: none;
    }
  </style>
  <style>
    div.relative {
      position: relative;
    } 
    div.absolute {
      position: absolute;
      top: 10px;
      right: 10px;
    }
  </style>

<div class="wrapper">

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>employee presence</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-calendar"></i> <?php date_default_timezone_set("Asia/Jakarta");echo date('l, d-M-Y H:i:s');?></a></li>
      </ol>
    </section>
    <section class="content">
      <!-- Bubble News -->
      <div class="row">
        <div class="col-xs-6 col-sm-6 col-lg-3">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>{{$data['permanen_pre']}}<sup style="font-size: 20px">%</sup></h3>

              <p>{{$qty_permanent}} Persons ({{$qty_permanent}}/{{$qty_active}})</p>
            </div>
            <div class="icon">
              <i class="ion ion-person"></i>
            </div>
            <a href="#" class="small-box-footer">Permanent {{$pre_permanent}}% ({{$qty_permanent}}/{{$qty_sai}})</a>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-3">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>{{$data['kontrak_pre']}}<sup style="font-size: 20px">%</sup></h3>

              <p>{{$qty_kontrak}} Persons ({{$qty_kontrak}}/{{$qty_active}})</p>
            </div>
            <div class="icon">
              <i class="ion ion-person"></i>
            </div>
            <a href="#" class="small-box-footer">Contract {{$pre_kontrak}}% ({{$qty_permanent}}/{{$qty_sai}})</a>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-3">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>{{$data['magang_pre']}}<sup style="font-size: 20px">%</sup></h3>

              <p>{{$qty_magang}} Persons ({{$qty_magang}}/{{$qty_active}})</p>
            </div>
            <div class="icon">
              <i class="ion ion-person"></i>
            </div>
            <?php 
            $ideal=round($qty_sai*20/100);
            if($ideal<$qty_magang)$over=$qty_magang-$ideal;
            else $over=0;
            ?>
            <a href="#" class="small-box-footer" title="{{$qty_magang}}/{{$qty_sai}}, Ideal:{{$ideal}} Over:{{$over}}">
              Magang {{$pre_magang_comp}}% ({{$qty_magang}}/{{$qty_sai}})
            </a>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-3">
          <!-- small box -->
          <div class="small-box bg-primary">
            <div class="inner">
              <h3>{{$qty_active}} <sup style="font-size: 20px">person</sup></h3>

              <p>&nbsp;</p>
            </div>
            <div class="icon">
              <i class="ion ion-person"></i>
            </div>
            <a href="#" class="small-box-footer">Permanet, Contract, Magang</a>
          </div>
        </div>
      </div>
      <!-- Graph & Lates Contract -->
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-8">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Company Address</h3>

            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="row">
                <div class="col-xs-8 col-sm-9 col-lg-9">
                  <div class="pad">

                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.8776078543788!2d107.2778228145891!3d-6.279817295454778!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6982b1fd938ecf%3A0x703d7b015ef5e930!2sPT%20Summit%20Adyawinsa%20Indonesia!5e0!3m2!1sid!2sid!4v1620021247397!5m2!1sid!2sid" width="105%" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                  </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4 col-sm-3 col-lg-3">
                  <div class="pad box-pane-right bg-green" style="min-height: 375px">
                   <div class="description-block margin-bottom">
                      <div class="sparkbar pad" data-color="#fff"><i class="fa fa-users fa-2x"></i></div>
                      <h5 class="description-header">{{$n_total}}</h5>
                      <span class="description-text">Employees</span>
                    </div>
                    <!-- /.description-block -->
                    <div class="description-block margin-bottom">
                      <div class="sparkbar pad" data-color="#fff"><i class="fa fa-bar-chart fa-2x"></i></div>
                      <h5 class="description-header">{{$p_area}}%</h5>
                      <span class="description-text">{{$nilai}}</span>
                    </div>
                    <!-- /.description-block -->
                    <div class="description-block">
                      <div class="sparkbar pad" data-color="#fff"><i class="fa fa-bar-chart fa-2x"></i></div>
                      <h5 class="description-header">{{$p_luar}}%</h5>
                      <span class="description-text">Luar {{$nilai}}</span>
                    </div>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-body -->
          </div>
          <?php if(isset($tb_status)&&request()->user()->hasRole('hr_access')){?>
            <div class="box box-primary">
              <div class="box-header">
                <i class="fa fa-bar-chart"></i>
                <h3 class="box-title">Contract Summary <?php echo date('d F Y');?></h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  <button type="button" class="btn btn-default btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body" style="padding:0px 20px 0px 20px;">
                <div style="width: 100%;">
                  <canvas id="canvas"></canvas>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer" style="border:0px;">
                <div class="box box-info collapsed-box" style="border:0px;">
                  <div class="box-header with-border">
                    <i class="fa fa-star"></i>
                    <h3 class="box-title" title="{{$periode}}"><label>Finish Contact on 1 Month Later: {{$qty_kontrak}} person</label></h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-default btn-xs" data-widget="collapse"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                  <div class="box-body" style="padding:10px;">

                    <?php if(isset($tb_kontrak)){?>
                      <table id="table3" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th style="width:30px;">No</th>
                            <th>NIK</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Contract</th>
                            <th>Finish Contract</th>
                            <th style="width:20px;">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $no=0;?>
                          @foreach($tb_kontrak as $dt)
                          <tr>
                            <td><?php $no++;echo $no;?></td>
                            <td>{{$dt->NIK}}</td>
                            <td>{{$dt->employee_name}}</td>
                            <td>{{$dt->dept_code}}</td>
                            <td>{{$dt->contract_name}}</td>
                            <td>{{$dt->finish_contract}}</td>
                            <td>&nbsp;</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    <?php }?>

                  </div>
                  <!-- /.box-body -->
                </div>
              </div>

            </div>
          <?php }?>
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-4">
          <div class="box box-solid">
            <div class="box-header bg-teal-gradient">
              <i class="fa fa-calendar"></i>
              <h3 class="box-title">Working Calendar</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                </button>
              </div>
            </div>
            <div class="box-body border-radius-none">
              <div class="chart">
                <div id="calendar"></div>
              </div>
            </div>
          </div>
 
          <!-- Upcoming Holidays Widget -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <i class="fa fa-star text-yellow"></i>
              <h3 class="box-title">Upcoming Holidays</h3>
            </div>
            <div class="box-body no-padding">
              <ul class="nav nav-pills nav-stacked">
                @forelse($upcomingHolidays as $holiday)
                  <?php 
                    if($holiday->category=='Holiday') {
                      $labelClass='label-danger';
                      $iconClass='fa-calendar-check-o';
                    } elseif($holiday->category=='Leave') {
                      $labelClass='label-warning';
                      $iconClass='fa-user-times';
                    } else {
                      $labelClass='label-info';
                      $iconClass='fa-info-circle';
                    }
                  ?>
                  <li>
                    <a href="#" title="{{ $holiday->description }}">
                      <i class="fa {{$iconClass}}"></i> {{ date('D, d M Y', strtotime($holiday->date_off)) }} 
                      {{ $holiday->short_description }}
                      <span class="label {{$labelClass}} pull-right">{{ $holiday->category }}</span>
                    </a>
                  </li>
                @empty
                  <li><a href="#" class="text-center text-muted">No upcoming holidays</a></li>
                @endforelse
              </ul>
            </div>
          </div>

        </div>
      </div>
    </section>
  </div>
</div>

<div class="modal fade" id="modal-info">
  <div class="modal-dialog box box-primary" style="width:95%;">
    <div class="modal-content" style="height:100%;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><i class="fa fa-info"></i>&nbsp;Update Information</h4>
      </div>
      <div class="modal-body relative">
        <div class="absolute">
          <button type="button" class="btn btn-primary" id="stopnotif" data-dismiss="modal">Dont show again</button>        
        </div>
        
        <img src="{{ asset('public/gambar/notif_update.png') }}" style="width:100%;">
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
</div>
<div class="modal showSweetAlert " data-backdrop="static" style="background: none !important;" data-animation="pop" tabindex="-1" id="notifModal" role="dialog">
  <div class="modal-dialog " role="document" >
    <div class="modal-content " >
      <div class="modal-header bg-warning">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
        <h3 class="modal-title"><i class="fa fa-info"></i>&nbsp; Notifications</h3>
      </div>
      <div class="modal-body">
      <h4 class="text-center">Anda memiliki data yang membutuhkan Approval</h4>
  <div clas="table-responsive">
          <table class="table table-light " id="notifi" style="width: 100%;">
            <thead>
              <tr>
                <th style="width:20%"></th>
                <th style="width:20%"></th>
                <th style="width:5%" ></th>
              </tr>
            </thead>
          <tbody>
                <tr>
                  <td><i class="fa fa-address-book"></i> <b>KSK</b></td>
                  <td id="kskcount"></td>
                  <td class="text-right"><a class="btn btn-primary btn-sm" href="Employees/KSK/0">Go >></a></td>
                </tr>
                <tr>
                  <td><i class="fa fa-file-archive-o"></i> <b>Overtime (SPL)</b></td>
                  <td id="splcount"></td>
                  <td class="text-right"><a class="btn btn-primary btn-sm" href="ApprovalSPL">Go >></a></td>
                </tr>
                <tr>
                  <td><i class="fa fa-sign-out"></i> <b>Leave (Cuti)</b></td>
                  <td id="cuticount"></td>
                  <td class="text-right"><a class="btn btn-primary btn-sm" href="Leave/Approves/0/0">Go >></a></td>
                </tr>
              </tbody>
          </table>
        </div>
       {{-- <img src="{{ asset('public/gambar/notif_update.png') }}" style="width:100%;"> --}}
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
</div>

@endsection
@section('Scripts')
	
	<script src="{{ asset('/assets/js/Chart.min.js') }}"></script>
	<script src="{{ asset('/assets/js/utils.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
	<!-- Grafik Bar/Line -->
  <script>
		var chartData = {
			labels: [
				@foreach($date_labels as $label)
                '{{ $label }}',
                @endforeach
			],
			datasets: [{
				type: 'line',
				label: 'Kontrak',
				backgroundColor: '#0000CC',
				data: [
					@foreach($chart_kontrak as $val)
                    '{{ $val }}',
                    @endforeach
				],
				yAxisID: 'y-axis-1',
				//borderColor: 'white',
				//borderWidth: 2
			}, {
				type: 'line',
				label: 'Magang',
				backgroundColor: '#CC0000',
				data: [
					@foreach($chart_magang as $val)
                    '{{ $val }}',
                    @endforeach
				],
				yAxisID: 'y-axis-1',
			}]

		};
		window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myMixedChart = new Chart(ctx, {
				type: 'bar',
				data: chartData,
				options: {
					responsive: true,
					title: {
						display: true,
						text: ''
					},
					legend: {
						position: 'right'
					},
					tooltips: {
						mode: 'index',
						intersect: true
					},
					responsive: true,
					scales: {
						xAxes: [{
							stacked: true,
							display: true,
							scaleLabel: {
								display: true,
								labelString: 'Date Finish'
							}
						}],
						yAxes: [{
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							stacked: true,
							display: true,
							
							scaleLabel: {
								display: true,
								labelString: 'Qty Employee'
							},
							position: 'left',
							id: 'y-axis-1',
								ticks: {
									beginAtZero: true,
									stepSize: 10,
								}
						}, {
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: false,
							position: 'right',
							id: 'y-axis-2',
							ticks: {
								callback: function (value) {
								return value.toLocaleString('de-DE', {style:'percent'});
								},
							},

							// grid line settings
							gridLines: {
								drawOnChartArea: false, // only want the grid lines for one axis to show up
							},
						}],
					},
					plugins: {
						datalabels: {
							anchor: 'end',
							align: 'top',
							formatter: Math.round,
							font: {
								weight: 'bold'
							}
						}
					}


				}
			});
			
		};
	</script>

  <script>
    $(function () {
      $('#table4').DataTable({
        'paging'      : true,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : false,
        'info'        : true,
        "pageLength"  : 5,
        'autoWidth'   : false,
        "lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "scrollX"     : true,
        'pagingType'  :"numbers"
      })
      $('#table5').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : false,
        'info'        : true,
        "pageLength"  : 5,
        'autoWidth'   : false,
        'lengthMenu': [[5,10, 25, 50,100, -1], [10, 25, 50,100, 'All']],
        'pagingType':"numbers"
      })
      $('#table2').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : false,
      'info'        : true,
      'autoWidth'   : false,
      'lengthMenu'  : [[10, 25, 50,100, -1], [10, 25, 50,100, 'All']],
      'pagingType'  :"numbers"
      })
      $('#table3').DataTable({
        'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : false,
      'info'        : true,
      "pageLength"  : 5,
      'autoWidth'   : false,
      'lengthMenu': [[5,10, 25, 50,100, -1], [10, 25, 50,100, 'All']],
      'pagingType':"numbers"
      })
    })
  </script>
  
  <!-- Page Calendar -->
  <script>
    $(function () {

      /* initialize the external events
      -----------------------------------------------------------------*/
      function init_events(ele) {
        ele.each(function () {

          // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
          // it doesn't need to have a start or end
          var eventObject = {
            title: $.trim($(this).text()) // use the element's text as the event title
          }

          // store the Event Object in the DOM element so we can get to it later
          $(this).data('eventObject', eventObject)

          // make the event draggable using jQuery UI
          $(this).draggable({
            zIndex        : 1070,
            revert        : true, // will cause the event to go back to its
            revertDuration: 0  //  original position after the drag
          })

        })
      }

      init_events($('#external-events div.external-event'))

      /* initialize the calendar
      -----------------------------------------------------------------*/
      //Date for the calendar events (dummy data)
      var date = new Date()
      var d    = date.getDate(),
          m    = date.getMonth(),
          y    = date.getFullYear()
      $('#calendar').fullCalendar({
        header    : {
          left  : 'title',
          center: '',
          right : 'prev,next today'
        },
        buttonText: {
          today: 'today',
          month: 'month',
          week : 'week',
          day  : 'day'
        },
        //Random default events
        events    : [
          <?php foreach($tb_freeday as $row){
          $y=date('Y',strtotime($row->date_off));
          $m=date('m',strtotime($row->date_off))-1;
          $j=date('j',strtotime($row->date_off));
          if($row->category=='Holiday')$latar='#f56954';
          elseif($row->category=='Leave')$latar='#f39c12';
          else $latar='#00c0ef';
          ?>
          {
            title          : '{{$row->category}}',
            start          : new Date({{$y}}, {{$m}}, {{$j}}),
            backgroundColor: '{{$latar}}',
            url            : '', 
            borderColor    : '{{$latar}}' 
          },
          <?php }?>
          {
            title          : 'today',
            start          : new Date(y,m,d),
            backgroundColor: '#5ad5d5',
            url            : '', 
            borderColor    : '#5ad5d5' 
          }
        ],
        height:400,
        contentHeight: 370,
        editable  : true,
        droppable : true, // this allows things to be dropped onto the calendar !!!
        drop      : function (date, allDay) { // this function is called when something is dropped

          // retrieve the dropped element's stored Event Object
          var originalEventObject = $(this).data('eventObject')

          // we need to copy it, so that multiple events don't have a reference to the same object
          var copiedEventObject = $.extend({}, originalEventObject)

          // assign it the date that was reported
          copiedEventObject.start           = date
          copiedEventObject.allDay          = allDay
          copiedEventObject.backgroundColor = $(this).css('background-color')
          copiedEventObject.borderColor     = $(this).css('border-color')

          // render the event on the calendar
          // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
          $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)

          // is the "remove after drop" checkbox checked?
          if ($('#drop-remove').is(':checked')) {
            // if so, remove the element from the "Draggable Events" list
            $(this).remove()
          }

        }
      })

      /* ADDING EVENTS */
      var currColor = '#3c8dbc' //Red by default
      //Color chooser button
      var colorChooser = $('#color-chooser-btn')
      $('#color-chooser > li > a').click(function (e) {
        e.preventDefault()
        //Save color
        currColor = $(this).css('color')
        //Add color effect to button
        $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
      })
      $('#add-new-event').click(function (e) {
        e.preventDefault()
        //Get value and make sure it is not null
        var val = $('#new-event').val()
        if (val.length == 0) {
          return
        }

        //Create events
        var event = $('<div />')
        event.css({
          'background-color': currColor,
          'border-color'    : currColor,
          'color'           : '#fff'
        }).addClass('external-event')
        event.html(val)
        $('#external-events').prepend(event)

        //Add draggable funtionality
        init_events(event)

        //Remove event from text input
        $('#new-event').val('')
      })
    })
  </script>

  <script type="text/javascript">
    $(function(){
      $("#stopnotif").click(function(){
        var email="{{$email}}";
        $.ajaxSetup({
          type:"POST",
          url: "/EMS/Dashboard/Notif",
          cache: false,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          data:{email:email},
          success: function(respond){
            if(respond!='Sukses'){
              alert(respond);
            }
          }
        })
        //alert(v_kolom);
      });
    })
  </script>
	<script>
		$( document ).ready(function() {
      var qtyksk="{{$qty_ksk}}";
      var notif="{{$notif}}";
      if(notif==1){
        $('#modal-info').modal('show');
      }

      $('#notifi').DataTable({
				'paging'      : false,
			'lengthChange': false,
			'searching'   : false,
			'ordering'    : false,
			'info'        : false,
			"pageLength"  : 10,
			'autoWidth'   : true,
      
			})
      var data ={
        _token : document.querySelector('meta[name="csrf-token"]')
						.getAttribute('content')
      }
      Notifikasi()
    function Notifikasi(){
        $.ajax({
          type: "POST",
          url: "{{ route('Dashboard.Notifikasi') }}",
          data: data,
          dataType: "json",
          success: function (data) {
            $(".modal").css({ 'background-color' : '', 'opacity' : '' });
            if(data.countKSK > 0 || data.countSPL > 0 || data.countLeave > 0 )
            {
            $("#kskcount").html(data.countKSK)
            $("#splcount").html(data.countSPL)
            $("#cuticount").html(data.countLeave)

           $("#notifModal").modal('show');
          }
          }
        });
      }
    });
	</script>

@endsection
