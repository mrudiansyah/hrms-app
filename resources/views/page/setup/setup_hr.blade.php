@extends('layouts/admin')
@section('Contents')
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
				Setup Utility
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12 col-md-12 col-lg-12">
					<div class="box box-danger">
						<div class="box-header">
							<h3 class="box-title">Lock Transaction</h3>
						</div>
						<!-- /.box-header -->
						<div class="box-body table-responsive">
						<table class="table table-hover">
							<tr style="background:#CCCCCC;">
								<th style="width:3px;">No</th>
								<th style="width:60px;">STATUS</th>
								<th style="width:60px;">LIMIT</th>
								<th style="width:180px;">FEATURE</th>
								<th>DESCRIPTION</th>
							</tr>
							<?php $no=0;?>
							@foreach($data['tb_utilities'] as $dt)
								<tr>
									<td><?php $no++;echo $no;?></td>
									<td style="text-align:center;">
										@if($dt->status==1)
											<i class="update_approval fa fa-check-square-o" data-id="{{$dt->id}}" data-status="0"></i>
										@else
											<i class="update_approval fa fa-square-o" data-id="{{$dt->id}}" data-status="1""></i>
										@endif
									</td>
									<td><input style="width:60px;" type="number" class="limit_transaksi" id="limit_transaksi_{{$dt->id}}" data-id="{{$dt->id}}" value="{{$dt->limit_transaksi}}" <?php if($dt->limit_transaksi==0||$dt->status==0)echo "disabled";?>></td>
									<td>{{$dt->atribut}}</td>
									<td>{{$dt->description}}</td>
								</tr>
							@endforeach
						</table>
						</div>
						<!-- /.box-body -->
					</div>
					<!-- /.box -->
				</div>
			</div>
			<!-- /.row -->
		</section>
		<!-- /.content -->
  	</div>
    <!-- /.Content -->
	@if ($errors->any())
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:65px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				{{$errors}}
		</div>
	@endif

@endsection
@section('Scripts')
	<script>
		$(document).on('click', '.update_approval', function() {
			//alert(c);
            var a=$(this).data('id');
            var b=$(this).data('status');

			if (confirm('Apakah Anda yakin?')) {
				$('#modal-loading').modal('show');
				var datas = {
					id:a,
					status:b
				}
				$.ajaxSetup({
					type:"POST",
					url: "{{$site}}/Setup/Update",
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
			}
		});
	</script>
	<script>
		$(document).on('click', '.limit_transaksi', function() {
			//alert(c);
            var a=$(this).data('id');
            var b='#limit_transaksi_'+a;
			var c=$(b).val();
			if (confirm('Apakah Anda yakin?')) {
				$('#modal-loading').modal('show');
				var datas = {
					id:a,
					limit_transaksi:c
				}
				$.ajaxSetup({
					type:"POST",
					url: "{{$site}}/Setup/UpdateLimit",
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
			}
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
			"scrollX"     : true
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
	
@endsection

