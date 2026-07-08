@extends('layouts/admin')
@section('Contents')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- Contents -->
   	<style>
		#tablesx th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#tables th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#tables tbody tr:hover{
			cursor:pointer;
		}
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 tbody tr:hover{
			cursor:pointer;
		}
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table4 tbody tr:hover{
			cursor:default;
		}
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Employee
				<small>list</small>
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
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title">Data Tables</h3>
						<div class="box-tools pull-right">
							<input type="month" id="periode" value="{{$periode}}">
							<button type="button" class="btn btn-success btn-xs" id="capture"><i class="fa fa-camera"></i> &nbsp;Capture</button>
							<button type="button" class="btn btn-danger btn-xs delete-modal" data-delid="{{$periode}}" data-delname="{{$periode}}"><i class="fa fa-trash"></i> &nbsp;Delete</button>
							<a href='/Admin/Employee'><button type="button" class="btn btn-default btn-xs"><i class="fa fa-home"></i> &nbsp;Back</button></a>
						</div>
					</div>
					<div class="box-body">
						<div style="padding:20px;overflow-x: scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>ID</th>
									<th>PIN</th>
									<th>EMPLOYEE_NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>DEPARTMENT</th>
									<th>POSITION</th>
									<th>STATUS</th>
									<th>START_CONTRACT</th>
									<th>FINISH_CONTRACT</th>
									<th>GENDER</th>
									<th>LINE</th>
									<th>TAX</th>
									<th>BIRTH_CITY</th>
									<th>DATE_BIRTH</th>
									<th>KABUPATEN</th>
									<th>TELEPON</th>
									<th>BLOOD</th>
									<th>RELIGION</th>
									<th>EDUCATION</th>
									<th>PROGRAM</th>
									<th>MOTHER</th>
									<th>KTP</th>
									<th>NPWP</th>
									<th>BANK_ACCOUNT</th>
									<th>KK</th>
									<th>BPJS_KES</th>
									<th>BPJS_KET</th>
									<th>EMERGENCY</th>
									<th>RELATION</th>
									<th>CONTACT</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_employee as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->id}}</td>
									<td>{{$dt->PIN}}</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td>{{$dt->department}}</td>
									<td>{{$dt->position}}</td>
									<td>{{$dt->status}}</td>
									<td>{{$dt->start_contract}}</td>
									<td>{{$dt->finish_contract}}</td>
									<td>{{$dt->gender}}</td>
									<td>{{$dt->line}}</td>
									<td>{{$dt->tax}}</td>
									<td>{{$dt->birth_city}}</td>
									<td>{{$dt->date_birth}}</td>
									<td>{{$dt->kabupaten}}</td>
									<td>{{$dt->telepon}}</td>
									<td>{{$dt->blood}}</td>
									<td>{{$dt->religion}}</td>
									<td>{{$dt->education}}</td>
									<td>{{$dt->program}}</td>
									<td>{{$dt->mother}}</td>
									<td>{{$dt->KTP}}</td>
									<td>{{$dt->NPWP}}</td>
									<td>{{$dt->bank_account}}</td>
									<td>{{$dt->KK}}</td>
									<td>{{$dt->bpjs_kes}}</td>
									<td>{{$dt->bpjs_ket}}</td>
									<td>{{$dt->emergency}}</td>
									<td>{{$dt->relation}}</td>
									<td>{{$dt->contact}}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						</div>
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

	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname1"></b> ?
					<input type="hidden" id="delid1">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes, Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>


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
	</script>
	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/Admin/Employee/Record/Show/"+periode;
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
				"pageLength"  : 10,
				'autoWidth'   : false,
				"pagingType": "full",
				"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
        //"iDisplayLength": 50
				//dom: 'Bfrtip',buttons: ['print']
			});
		
			new $.fn.dataTable.Buttons( table, {
				buttons: ['copy', 'excel', 'print']
			} );
		
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );


	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		$('.box-header').on('click', '#capture', function() {
			var periode=$('#periode').val();
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Employee/Record/Submit",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{periode:periode},
				success: function(respond){
					// if(respond=="Sukses"){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid1').val($(this).data('delid'));
			$('#delname1').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});

		$('.modal-footer').on('click', '.delete', function() {
			var periode=$('#delid1').val();
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Employee/Record/Update",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{periode:periode},
				success: function(respond){
					// if(respond=="Sukses"){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});
	</script>
@endsection
