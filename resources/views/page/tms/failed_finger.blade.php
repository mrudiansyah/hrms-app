@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
		#tables th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		vertical-align:middle;
		text-align:left;
		}	
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table5 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Manual Checktime
				<small>failed finger</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set("Asia/Jakarta");
						echo date('l, d M Y H:i');
					?>
				</a>
				</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary" style="background:#FFF;">
						<div class="box-body" style="min-height:200px;overflow-x:scroll;">
							<div class="row" style="padding-bottom:10px;">
								<div class="col-lg-2 col-xs-6">
									From: 
									<input type="date" class="form-control" id="start" name="start" value="{{$data['start']}}"> 
								</div>
								<div class="col-lg-2 col-xs-6">
									To:
									<input type="date" class="form-control" id="finish" name="finish" value="{{$data['end']}}">
								</div>
							</div>
							<table id="tables" class="table table-striped" border="0">
								<thead>
									<tr>
										<th style="width:30px;">NO</th>
										<th>REF_CHECKTIME</th>
										<th>NIK</th>
										<th>NAME</th>
										<th>POSITION</th>
										<th>DEPARTMENT</th>
										<th>FINGER</th>
										<th>STATUS</th>
										<th>REMARK</th>
									</tr>
								</thead>
								<tbody>
								<?php $no=0;?>
									@foreach($data['tb1'] as $dt)
									<tr>
										<td><?php $no++;echo $no;?></td>
										<td>{{$dt->id_checktime}}</td>
										<td>{{$dt->NIK}}</td>
										<td>{{$dt->nama_karyawan}}</td>
										<td>{{$dt->department}}</td>
										<td>{{$dt->jabatan}}</td>
										<td>{{$dt->checktime_draft}}</td>
										<td>
											@if($dt->status==0)
												<i class="fa fa-square-o confirm" data-id_checktime="{{$dt->id_checktime}}"></i>
											@else
												<i class="fa fa-check-square-o"></i>
											@endif
										</td>
										<td>{{$dt->confirmed_at}}</td>
									</tr>
									@endforeach
								<tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>

				</div>
			</div>
			<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->


    @if ($message = Session::get('success'))
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
		</div>
    @endif

@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table2').DataTable({
				'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table4').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})	
	</script>
	<!-- Durasi Alert --->
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Approve Data
		$(document).on('click', '.approve-modal', function() {
			$('#approveid').val($(this).data('approveid'));
			$('#approvename').text($(this).data('approvename'));
			$('#modal-approve').modal('show');
		});
		$('.modal-footer').on('click', '.approve', function() {
			var x=$('#approveid').val();
			window.location.href='/Leave/Report/'+x+'/1';
		});
	</script>
	<script>
      $(document).ready(function() {
        var table = $('#tables').DataTable({
          'paging'      : true,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 25,
          'autoWidth'   : true,
          "lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        });
      
        new $.fn.dataTable.Buttons( table, {
          //buttons: ['copy', 'excel', 'print']
			buttons: [
				{ extend: 'copyHtml5', footer: true },
				{ extend: 'excelHtml5', footer: true },
				{ extend: 'print', footer: true }
			]

        } );
      
        table.buttons( 0, null ).container().prependTo(
          table.table().container()
        );
      } );


  	</script>

	<script>
		$('body').on("change","#start",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/FailedFinger/"+start+"/"+finish;
		});
		$('body').on("change","#finish",function(){
			var start=document.getElementById('start').value;
			var finish=document.getElementById('finish').value;
			window.location.href="/FailedFinger/"+start+"/"+finish;
		});
	</script>
	<script>
		$(".confirm").click(function(){
			$.ajaxSetup({
				type:"POST",
				url: "/FingerConfirm",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var id_checktime=$(this).data('id_checktime');
			$.ajax({
				data:{id_checktime:id_checktime},
				success: function(respond){
					location.reload();
					//alert(respond);
				}
			})
		});

	</script>


@endsection
