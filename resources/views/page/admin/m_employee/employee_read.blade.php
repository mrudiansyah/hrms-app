@extends('layouts/admin')
@section('Contents')
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
							<a href='/Admin/Employee/Create'><button type="button" class="btn btn-primary btn-md"><i class="fa fa-plus"></i> &nbsp;Add New</button></a>
							<a href='/Admin/EmployeeBPJS'><button type="button" class="btn btn-success btn-md"><i class="fa fa-user-plus"></i> &nbsp;BPJS</button></a>
							<a href='/Admin/Employee/Domiciles'><button type="button" class="btn btn-warning btn-md"><i class="fa fa-map-marker"></i> &nbsp;Domiciles</button></a>
							<a href='/Admin/Employee/PSAB'><button type="button" class="btn btn-default btn-md"><i class="fa fa-user"></i> &nbsp;PSAB</button></a>
							<a href='/Admin/Employee/Other/0'><button type="button" class="btn btn-default btn-md"><i class="fa fa-user"></i> &nbsp;PKL</button></a>
							<a href='/Admin/Employee/Record/Show/0'><button type="button" class="btn btn-info btn-md"><i class="fa fa-user"></i> &nbsp;Recap</button></a>
							<a href='/Admin/Employee/Education'><button type="button" class="btn btn-info btn-md"><i class="fa fa-mortar-board"></i> &nbsp;Eduction</button></a>
						</div>
					</div>
					<div class="box-body">
						<div style="padding:20px;overflow-x: scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th>No</th>
									<th>ID</th>
									<th>PIN</th>
									<th>EMPLOYEE_NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>DEPARTMENT</th>
									<th>POSITION</th>
									<th>STATUS</th>
									<th>JOIN_DATE</th>
									<th>START_CONTRACT</th>
									<th>FINISH_CONTRACT</th>
									<th>GENDER</th>
									<th>LINE</th>
									<th>TAX</th>
									<th>BIRTH_CITY</th>
									<th>DATE_BIRTH</th>
									<th>KABUPATEN</th>
									<!--
									<th>ADDRESS KTP</th>
									<th>DOMICILES</th>
									-->
									<th>TELEPON</th>
									<th>BLOOD</th>
									<th>RELIGION</th>
									<!-- <th>EDUCATION</th>
									<th>PROGRAM</th> -->
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
									<!--
									<th>ADDRESS</th>
									-->
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_employee as $dt)
								<tr>
									<td style="text-align:left;padding-left:0px;">
										<a title="Show" href='/Admin/Employee/Update/<?php echo $dt->id;?>'><button type="button" class="btn btn-primary btn-xs"><i class="fa fa-folder-open-o"></i></button></a>
									</td>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->id}}</td>
									<td>{{$dt->badgenumber}}</td>
									<td>{{$dt->NIK}}</td>
									<td>{{$dt->employee_name}}</td>
									<td>{{$dt->dept_code}}</td>
									<td>{{$dt->position_name}}</td>
									<td>{{$dt->contract_name}}</td>
									<td>{{$dt->join_date}}</td>
									<td>{{$dt->start_contract}}</td>
									<td>{{$dt->finish_contract}}</td>
									<td>{{$dt->gender}}</td>
									<td>{{$dt->line}}</td>
									<td>{{$dt->kode_status}}</td>
									<td>{{$dt->tempat_lahir}}</td>
									<td>{{$dt->tanggal_lahir}}</td>
									<td>{{$dt->kabupaten}}</td>
									<!--
									<td>{{$dt->detail}} {{$dt->kelurahan}} {{$dt->kecamatan}} {{$dt->kabupaten}} {{$dt->provinsi}}</td>
									<td>{{$dt->dom_detail}} {{$dt->dom_kelurahan}} {{$dt->dom_kecamatan}} {{$dt->dom_kabupaten}} {{$dt->dom_provinsi}}</td>
									-->
									<td>{{$dt->nomor_telepon}}</td>
									<td>{{$dt->golongan_darah}}</td>
									<td>{{$dt->agama}}</td>
									<!-- <td>{{$dt->top_education}}</td>
									<td>{{$dt->prodi}}</td> -->
									<td>{{$dt->ibu_kandung}}</td>
									<td>{{$dt->nomor_ktp}}</td>
									<td>{{$dt->nomor_npwp}}</td>
									<td>{{$dt->nomor_rekening}}</td>
									<td>{{$dt->nomor_kk}}</td>
									<td>{{$dt->nomor_bpjs_kes}}</td>
									<td>{{$dt->nomor_bpjs_ket}}</td>
									<td>{{$dt->nama_keluarga}}</td>
									<td>{{$dt->hubungan}}</td>
									<td>{{$dt->nomor_kontak}}</td>
									<!--
									<td>{{$dt->detail_kontak}} {{$dt->kelurahan_kontak}} {{$dt->kecamatan_kontak}} {{$dt->kabupaten_kontak}} {{$dt->provinsi_kontak}}</td>
									-->
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
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid1').val($(this).data('delid'));
			$('#delname1').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid1').val();
			window.location.href='/Admin/Employee/Delete/'+x;
		});
	</script>
@endsection
