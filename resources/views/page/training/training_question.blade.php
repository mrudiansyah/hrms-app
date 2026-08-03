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
							<a href="/Training/Examination" title="Examination" type="button" class="btn btn-primary btn-xs"><i class="fa fa-angle-double-left"></i> &nbsp; Back</a>
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<table id="table2" class="table table-hover">
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
									<td>{{$dt->passing_grade}}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
			<div class="col-lg-6 col-md-12 col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>
						<h3 class="box-title">Question List for {{$test_name}}</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-success btn-xs form" data-idquestion="" data-indexquestion="0" data-question="Question" data-optiona="Option A" data-optionb="Option B" data-optionc="Option C" data-optiond="Option D" data-answercode=""><i class="fa fa-plus"></i> &nbsp;Add Question</button>
						</div>
					</div>
					<div class="box-body" style="overflow-x: scroll;">
						<?php $no=1;?>
						@foreach($tb_question as $dt)
						<?php $no++;?>
						<div class="col-md-12 col-lg-12 col-xs-12">
							<div class="box box-default">
								<div class="box-header with-border">
								<h3 class="box-title">
									<b class="label label-primary">{{$dt->index_question}}</b>
									<b class="label label-info">{{$dt->answer_code}}</b>
								</h3>

								<div class="pull-right">
									<button title="Edit" type="button" class="form btn btn-default btn-xs" data-idquestion="{{$dt->id}}" data-indexquestion="{{$dt->index_question}}" data-question="{{$dt->question}}" data-optiona="{{$dt->option_a}}" data-optionb="{{$dt->option_b}}" data-optionc="{{$dt->option_c}}" data-optiond="{{$dt->option_d}}" data-answercode="{{$dt->answer_code}}"><i class="fa fa-edit"></i></button>
									<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->question}}"><i class="fa fa-trash"></i></button>
								</div>
								<!-- /.box-tools -->
								</div>
								<!-- /.box-header -->
								<div class="box-body">
									{{$dt->question}}
									<ol type="A">
										<li>{{$dt->option_a}}</li>
										<li>{{$dt->option_b}}</li>
										<li>{{$dt->option_c}}</li>
										<li>{{$dt->option_d}}</li>
									</ol>
								</div>
								<!-- /.box-body -->
							</div>
						</div>
						@endforeach

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

	<div class="modal fade" id="modal-form">
		<div class="modal-dialog box box-success" style="width:600px;">
			<div class="modal-content">
					<form method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
						<div class="modal-header">	
							<b>FORM QUESTION</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-xs-12 col-md-6 col-lg-6">
									<input type="hidden" id="idComponent" name="id_question" class="form-control">
									<div class="form-group">
										<label>Index</label>
										<?php $max=$no-1;?>
										<input type="number" class="form-control" name="index_question" id="indexquestion" min="1" max="{{$max}}">								
									</div>
									<div class="form-group">
										<label>Question</label>
										<textarea class="form-control" rows="8" name="question" id="question"></textarea>
									</div>
									<div class="form-group">
										<label>Answer</label>
										<select class="form-control" name="answer_code" id="answercode">
											<option value=""></option>
											<option value="A">A</option>
											<option value="B">B</option>
											<option value="C">C</option>
											<option value="D">D</option>
										</select>
									</div>
								</div>
								<div class="col-xs-12 col-md-6 col-lg-6">
									<div class="form-group">
										<label>Option A</label>
										<textarea class="form-control" name="option_a" id="optiona"></textarea>
									</div>
									<div class="form-group">
										<label>Option B</label>
										<textarea class="form-control" name="option_b" id="optionb"></textarea>
									</div>
									<div class="form-group">
										<label>Option C</label>
										<textarea class="form-control" name="option_c" id="optionc"></textarea>
									</div>
									<div class="form-group">
										<label>Option D</label>
										<textarea class="form-control" name="option_d" id="optiond"></textarea>
									</div>
								</div>
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
			'paging'      : false,
			'lengthChange': true,
			'searching'   : false,
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
				var no="{{$no}}";
				var indexquestion=$(this).data('indexquestion');
				if(indexquestion==0){
					$('#indexquestion').val(no);
				}else{
					$('#indexquestion').val($(this).data('indexquestion'));
				}
				$('#idComponent').val($(this).data('idquestion'));
				
				$('#question').val($(this).data('question'));
				$('#optiona').val($(this).data('optiona'));
				$('#optionb').val($(this).data('optionb'));
				$('#optionc').val($(this).data('optionc'));
				$('#optiond').val($(this).data('optiond'));
				$('#answercode').val($(this).data('answercode'));
				$('#modal-form').modal('show');
			});
			$('.modal-footer').on('click', '#simpan', function() {
				var x=$('#idComponent').val();
				var idtest="{{$id_test}}";
				var indexquestion=$('#indexquestion').val();
				var question=$('#question').val();
				var optiona=$('#optiona').val();
				var optionb=$('#optionb').val();
				var optionc=$('#optionc').val();
				var optiond=$('#optiond').val();
				var answercode=$('#answercode').val();
				$.ajaxSetup({
					type:"POST",
					url: "/Training/Simpan/Question",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{id:x,idtest:idtest,indexquestion:indexquestion,question:question,optiona:optiona,optionb:optionb,optionc:optionc,optiond:optiond,answercode:answercode},
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
					url: "/Training/Delete/Question",
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
							alert(respond);
							// location.reload();
						}
					}
				})
			});
		// Delete End
	</script>
@endsection
