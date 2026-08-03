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
				Training
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-lg-6 col-md-12 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-list"></i>
						<h3 class="box-title">Training Test</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-success btn-xs form" data-idtest="" data-testname="" data-minutes="" data-passinggrade=""><i class="fa fa-plus"></i> &nbsp;Add New</button>
							<button type="button" class="btn btn-primary btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button type="button" class="btn btn-danger btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:30px;">No</th>
									<th>Examination</th>
									<th>Duration</th>
									<th>Passing Grade</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_training_test as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td>{{$dt->test_name}}</td>
									<td>{{$dt->minutes}}</td>
									<td>{{$dt->passing_grade}}
										<div class="pull-right">
											<a href="/Training/Question/{{$dt->id}}" title="Questions" type="button" class="participant btn btn-primary btn-xs"><i class="fa fa-file-text-o"></i></a>
											<button title="Edit" type="button" class="form btn btn-primary btn-xs" data-idtest="{{$dt->id}}" data-testname="{{$dt->test_name}}" data-minutes="{{$dt->minutes}}" data-passinggrade="{{$dt->passing_grade}}"><i class="fa fa-edit"></i></button>
											<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->test_name}}"><i class="fa fa-trash"></i></button>
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="col-lg-6 col-md-12 col-xs-12">
				&nbsp;
			</div>
		</div>
		<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->

	<div class="modal fade" id="modal-form">
		<div class="modal-dialog box box-success" style="width:350px;">
			<div class="modal-content">
					<form method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
						<div class="modal-header">	
							<b>FORM TEST</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="idComponent" name="id_test" class="form-control">
							<div class="form-group">
								<label>Test Name</label>
								<input type="text" class="form-control" name="test_name" id="testname">								
							</div>
							<div class="form-group">
								<label>Minutes</label>
								<input type="number" class="form-control" name="minutes" id="minutes">								
							</div>
							<div class="form-group">
								<label>Passing Grade</label>
								<input type="number" class="form-control" name="passing_grade" id="passinggrade">								
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-success pull-right" id="simpan">Submit</button>
						</div>
					</form>
			</div>

			<!-- /.modal-content -->
		</div>
	<!-- /.modal-dialog -->
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
		// Form
			$(document).on('click', '.form', function() {
				var docid=$(this).data('iddocument');
				$('#idComponent').val($(this).data('idtest'));
				$('#testname').val($(this).data('testname'));
				$('#minutes').val($(this).data('minutes'));
				$('#passinggrade').val($(this).data('passinggrade'));
				$('#modal-form').modal('show');
			});
			$('.modal-footer').on('click', '#simpan', function() {
				var x=$('#idComponent').val();
				var testname=$('#testname').val();
				var minutes=$('#minutes').val();
				var passinggrade=$('#passinggrade').val();

				$.ajaxSetup({
					type:"POST",
					url: "/Training/Simpan/Examination",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{id:x,testname:testname,minutes:minutes,passinggrade:passinggrade},
					success: function(respond){
						if(respond=='Sukses'){
							window.location.href = '/Training/Examination';
						}else{
							// alert(respond);
							location.reload();
						}
					}
				})
			});

		// Form End
		// Delete Data
			$(document).on('click', '.delete-modal', function() {
				$('#delid1').val($(this).data('delid'));
				$('#delname1').text($(this).data('delname'));
				$('#modal-delete').modal('show');
			});
			$('.modal-footer').on('click', '.delete', function() {
				var x=$('#delid1').val();

				$.ajaxSetup({
					type:"POST",
					url: "/Training/Delete/Examination",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{id:x},
					success: function(respond){
						if(respond=='Sukses'){
							location.reload();
						}else{
							// alert(respond);
							location.reload();
						}
					}
				})
			});
		// Delete End
	</script>
@endsection
