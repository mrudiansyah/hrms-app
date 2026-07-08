@extends('layouts/admin')
@section('Contents')
<meta name="csrf-token" content="{{ csrf_token() }}">
   <!-- Contents -->
   <style>
        tr:hover {
          background-color: #DCDCDC;
		  cursor:pointer;
        }
   </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
		<h1 onclick="">
			Other Employees
			<div class="pull-right">
				<button type="button"  class="btn btn-primary btn-md edit-modal" data-iddata="" data-kategori="" data-subkategori="" data-gender="" data-nama="" data-nomorktp="" data-ibukandung="" data-joindate="" data-deptcode="" data-pin="" data-tempatlahir="" data-tanggallahir="" data-nomorhp=""><i class="fa fa-plus"></i> &nbsp;Add New</button>
				<a href='/Admin/Employee'><button type="button" class="btn btn-success btn-md"><i class="fa fa-user"></i> &nbsp;Employee List</button></a>
			</div>
		</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-header">
					<i class="fa fa-user"></i>
					<h3 class="box-title">
						@if($type==0)
							PSAB & PKL
						@elseif($type=='Siswa')
							PKL Siswa
						@elseif($type=='Mahasiswa')
							PKL Mahasiswa
						@else
							Arsif
						@endif
					</h3>
					<div class="pull-right">
						<div class="btn-group">
							<button type="button" class="btn btn-default"><i class="fa fa-list">&nbsp;&nbsp;Select</i></button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li>
									<a href="/Admin/Employee/Other/Siswa">Siswa</i></a>
								</li>
								<li>
									<a href="/Admin/Employee/Other/Mahasiswa">Mahasiswa</i></a>
								</li>
								<li class="divider"></li>
								<li>
									<a href="/Admin/Employee/Other/Arsif">Arsif</i></a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			<div class="box-body">
				<table id="tables" class="table table-bordered">
					<thead>
						<tr>
							<th>No</th>
							<th>Category</th>
							<th>Type</th>
							<th>Name</th>
							<th>Gender</th>
							<th>KTP</th>
							<th>Born</th>
							<th>Contact</th>
							<th>Mother</th>
							<th>Join</th>
							<th>Dept</th>
							<th>PIN</th>
						</tr>
					</thead>
					<tbody>
						<?php $no=0;?>
						@foreach($table1 as $dt)
							<tr>
								<td><?php $no++;echo $no;?></td>
								<td>{{$dt->kategori}}</td>
								<td>{{$dt->sub_kategori}}</td>
								<td>{{$dt->nama}}</td>
								<td>{{$dt->gender}}</td>
								<td>{{$dt->nomor_ktp}}</td>
								<td>{{$dt->tempat_lahir}}, {{$dt->tanggal_lahir}}</td>
								<td>{{$dt->nomor_hp}}</td>
								<td>{{$dt->ibu_kandung}}</td>
								<td>{{$dt->join_date}}</td>
								<td>{{$dt->dept_code}}</td>
								<td>
									{{$dt->PIN}}
									<div class="pull-right">
										@if($type=='Arsif')
										<button type="button" class="btn btn-default btn-xs"><a href="/Admin/Employee/Create/Delete/{{$dt->id}}/OtherActive"><i class="fa fa-user-plus"></i></a></button>
										@else
											<button type="button" class="btn btn-primary btn-xs edit-modal" data-iddata="{{$dt->id}}" data-kategori="{{$dt->kategori}}" data-subkategori="{{$dt->sub_kategori}}" data-gender="{{$dt->gender}}" data-nama="{{$dt->nama}}" data-nomorktp="{{$dt->nomor_ktp}}" data-ibukandung="{{$dt->ibu_kandung}}" data-joindate="{{$dt->join_date}}" data-deptcode="{{$dt->dept_code}}" data-pin="{{$dt->PIN}}" data-tempatlahir="{{$dt->tempat_lahir}}" data-tanggallahir="{{$dt->tanggal_lahir}}" data-nomorhp="{{$dt->nomor_hp}}"><i class="fa fa-edit"></i></button>
											<button type="button" class="btn btn-danger btn-xs delete-modal" data-delid="{{$dt->id}}" data-delid1="Other" data-delname="{{$dt->nama}}"><i class="fa fa-trash-o"></i></button>
										@endif
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->

			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

    <div class="modal fade" id="modal-edit">
		<div class="modal-dialog" style="width:700px;">
			<div class="modal-content">
				<form>
					
					{{ csrf_field() }}
					<div class="modal-body">
						<div class="box box-primary box-solid" style="border:0px;">
							<div class="box-header">
								<label id="judul"></label>
							</div>
							<div class="box-body">
								<div class="row">
									<div class="col-xs-12 col-md-6 col-lg-6">
										<div class="form-group">
											<label>Kategori</label>
											<select class="form-control" id="kategori">
												<option value="PKL">PKL</option>
											</select>
										</div>
										<div class="form-group">
											<label>Sub Kategori</label>
											<select class="form-control" id="subkategori">
												<option value=""></option>
												<option value="Siswa">Siswa</option>
												<option value="Mahasiswa">Mahasiswa</option>
											</select>
										</div>
										<div class="form-group">
											<label>Jenis Kelamin</label>
											<select class="form-control" id="gender">
												<option value="Laki-laki">Laki-laki</option>
												<option value="Perempuan">Perempuan</option>
											</select>
										</div>
										<div class="form-group">
											<label>Nama</label>
											<input type="text" id="nama" class="form-control">
										</div>
										<div class="form-group">
											<label>Tempat Lahir</label>
											<input type="text" id="tempat_lahir" class="form-control">
										</div>
										<div class="form-group">
											<label>Tanggal Lahir</label>
											<input type="date" id="tanggal_lahir" class="form-control">
										</div>
									</div>
									<div class="col-xs-12 col-md-6 col-lg-6">
										<div class="form-group">
											<label>Nomor KTP</label>
											<input type="number" id="nomorktp" class="form-control">
											<input type="hidden" id="iddata" class="form-control">
										</div>
										<div class="form-group">
											<label>Nama Ibu Kandung</label>
											<input type="text" id="ibukandung" class="form-control">
										</div>
										<div class="form-group">
											<label>Join Date</label>
											<input type="date" id="joindate" class="form-control">
										</div>
										<div class="form-group">
											<label>Department</label>
											<select class="form-control" id="deptcode">
												<option value=""></option>
												@foreach ($table2 as $dt)
													<option value="{{$dt->dept_code}}">{{$dt->dept_code}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group">
											<label>PIN</label>
											<input type="number" id="pin" class="form-control">
										</div>
										<div class="form-group">
											<label>Nomor Ponsel</label>
											<input type="text" id="nomor_hp" class="form-control">
										</div>
									</div>
								</div>
							</div>
							<div class="box-footer">
								<button type="button" class="btn btn-success pull-right" id="simpan">Save</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
						</div>
					</div>
				</form>		
			</div>
		</div>
    </div>
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname"></b> ?
					<input type="hidden" id="delid">
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
		$('#table1').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		//"pageLength"  : 25,
		'autoWidth'   : false
		})
		$('#table2').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : true
		})
	})
	</script>
	<!-- page script alert-->
	<script>
		$(document).ready(function() {
		  var table = $('#tables').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
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
		$(document).on('click', '.edit-modal', function() {
			$('#iddata').val($(this).data('iddata'));
			$('#kategori').val($(this).data('kategori'));
			$('#subkategori').val($(this).data('subkategori'));
			$('#gender').val($(this).data('gender'));
			$('#nama').val($(this).data('nama'));
			$('#nomorktp').val($(this).data('nomorktp'));
			$('#ibukandung').val($(this).data('ibukandung'));
			$('#joindate').val($(this).data('joindate'));
			$('#deptcode').val($(this).data('deptcode'));
			$('#tempat_lahir').val($(this).data('tempatlahir'));
			$('#tanggal_lahir').val($(this).data('tanggallahir'));
			$('#nomor_hp').val($(this).data('nomorhp'));
			$('#pin').val($(this).data('pin'));
			var judul=$(this).data('nama');
			//alert(judul);
			if(judul==""){
				document.getElementById("judul").textContent = 'NEW DATA';
			}else{
				document.getElementById("judul").textContent = judul;
			}
			$('#modal-edit').modal('show');
		});
		$(document).on('click', '#simpan', function() {
			var iddata= $('#iddata').val();
			var kategori=$('#kategori').val();
			var subkategori=$('#subkategori').val();
			var gender=$('#gender').val();
			var nama=$('#nama').val();
			var nomorktp=$('#nomorktp').val();
			var ibukandung=$('#ibukandung').val();
			var joindate=$('#joindate').val();
			var deptcode=$('#deptcode').val();
			var pin=$('#pin').val();
			var tempatlahir=$('#tempat_lahir').val();
			var tanggallahir=$('#tanggal_lahir').val();
			var nomorhp=$('#nomor_hp').val();

			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Employee/Other",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{iddata:iddata,kategori:kategori,subkategori:subkategori,gender:gender,nama:nama,nomorktp:nomorktp,ibukandung:ibukandung,joindate:joindate,deptcode:deptcode,pin:pin,tempatlahir:tempatlahir,tanggallahir:tanggallahir,nomorhp:nomorhp},
				success: function(respond){
					//alert(respond);
					// if(respond!="<html><body><p>Success</p></body></html>"){
					// 	alert(respond);
					// }else{
					// 	window.location.reload();
					// }
					window.location.reload();
				}
			})
		});
	</script>
	<script type="text/javascript">
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delid1').val($(this).data('delid1'));
			$('#delname').text($(this).data('delname'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			var y=$('#delid1').val();
			window.location.href='/Admin/Employee/Create/Delete/'+x+'/'+y;
		});
	</script>

  @endsection
