@extends('layouts/admin')
@section('Contents')
<?php
//$serverName = "192.168.1.4"; //serverName\instanceName
//$connectionInfo = array( "Database"=>"db_ems_memo", "UID"=>"EMS", "PWD"=>"1nd()n3514572");
//$conn = sqlsrv_connect( $serverName, $connectionInfo);

?>
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <?php $user=Auth::user()->name;?>
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
		#table2 tbody tr:hover{
			cursor:pointer;
		}
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				General Memo
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>
						<h3 class="box-title">Archieve Overtime Request <small>(Tanggal < {{$data['today']}})</small></h3>
						<div class="box-tools pull-right">
							<a class="btn btn-default btn-md" href="/GeneralMemo/{{$data['category']}}">
								<i class="fa fa-file-o"></i> &nbsp;New Memo
							</a>
							<a class="btn btn-default btn-md" href="/ArchieveMemoDetail/{{$data['category']}}/{{$data['periode']}}">
								<i class="fa fa-file"></i> &nbsp;Detail Archieve
							</a>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-6 col-md-3 col-lg-2 pull-right">
								<input type="month" id="periode" class="form-control" value="{{$data['periode']}}" min="2025-04">	
							</div>
						</div>
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:50px;">No</th>
									<th style="width:70px;">No.Registration</th>
									<th style="width:70px;">Memo Category</th>
									<th style="width:70px;">Tanggal</th>
									<th>Remark</th>
									<th style="width:50px;">Approval</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($data['table2'] as $dt)
								<tr>
									<td>
										<?php $no++;echo $no;?>
									</td>
									<td>{{$dt->memo_number}}</td>
									<td>{{$dt->description}}</td>
									<td>{{$dt->date_information}}</td>
									<td>{{$dt->additional_note}}</td>
									<td>
										@if($dt->is_completed==3)
											<span class='badge bg-green'>1</span>
											<span class='badge bg-green'>2</span>
											<span class='badge bg-green'>3</span>
										@elseif($dt->is_completed==2)
											<span class='badge bg-green'>1</span>
											<span class='badge bg-green'>2</span>
											<span class='badge bg-white'>3</span>
										@elseif($dt->is_completed==1)
											<span class='badge bg-green'>1</span>
											<span class='badge bg-white'>2</span>
											<span class='badge bg-white'>3</span>
										@else
											<span class='badge bg-white'>1</span>
											<span class='badge bg-white'>2</span>
											<span class='badge bg-white'>3</span>
										@endif
										<div class="pull-right">
											@if($dt->is_draft=='1')
												<button class="btn btn-warning btn-xs create-modal" data-id_memo="{{$dt->id}}" data-id_template_memo="{{$dt->id_template_memo}}" data-date_information="{{$dt->date_information}}" data-additional_information="{{$dt->additional_note}}">
													<i class="fa fa-edit"></i>
												</button>
												<button class="btn btn-danger btn-xs delete-modal" data-delid="{{$dt->id}}" data-delid2="tb_memo" data-delname="{{$dt->memo_number}}">
													<i class="fa fa-trash"></i>
												</button>
											@endif
											<button class="btn btn-primary btn-xs">
												<a href="/GeneralMemo/Detail/{{$dt->id}}" style="color:white;"><i class="fa fa-folder-o"></i></a>
											</button>
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						&nbsp;
					</div>
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
					Click Yes to Delete : <b id="delname"></b> ?
					<input type="hidden" id="delid">
					<input type="hidden" id="delid2">
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
	<div class="modal fade" id="modal-create">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Form Memo</h4>
					<input type="hidden" name="id_memo" id="id_memo" class="form-control">
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="{{ csrf_token() }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="form-group">
								<label>Date</label>
								<input type="date" name="date_information" id="date_information" class="form-control">
							</div>
							<div class="form-group">
								<label>Category</label>
								<select name="id_template_memo" class="form-control" id="id_template_memo">
									<option value=""></option>
									@foreach($data['table3'] as $dt3)
										<option value="{{$dt3->id}}">{{$dt3->description}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label>General Note <small>(optional)</small></label>
								<input type="text" name="additional_note" id="additional_note" class="form-control">
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left createMemo" data-dismiss="modal">Save</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

@endsection
@section('Scripts')
	<script type="text/javascript">
		$(document).on('click', '.create-modal', function() {
            $("#id_memo").val($(this).data('id_memo'));
            $("#date_information").val($(this).data('date_information'));
            $("#additional_note").val($(this).data('additional_note'));
            $("#id_template_memo").val($(this).data('id_template_memo'));
			$('#modal-create').modal('show');
		});

		$('.modal-footer').on('click', '.createMemo', function() {
            var a=$("#id_memo").val();
			var b=$("#date_information").val();
			var c=$("#additional_note").val();
			var d=$("#id_template_memo").val();
            var datas = {
                id_memo:a,
				date_information:b,
				additional_note:c,
				id_template_memo:d
            }
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/GeneralMemo/Create",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:datas,
				success: function(respond){
					//alert(respond);
					if(respond==''){
						location.reload();
					}else{
						window.location.href='/GeneralMemo/Detail/' + respond;
					}
				}
			})
		});
		// Delete Data
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#delid2').val($(this).data('delid2'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			var y=$('#delid2').val();
            var datas = {
                id:x,
				tb_name:y
            }
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/GeneralMemo/Delete",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:datas,
				success: function(respond){
					//alert(respond);
					location.reload();
				}
			})
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
			"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
			"scrollX"     : true,
			"order"       : [[3, 'desc']]
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
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			var kategori="<?php echo $data['category'];?>";
			window.location.href="{{$site}}/ArchieveMemo/"+kategori+"/"+periode;
		});
	</script>
	
@endsection

