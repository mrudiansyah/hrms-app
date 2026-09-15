@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $user = request()->user();
        $canManageLeave = $user && ($user->hasRole('hr_access') || $user->hasRole('admin_department'));
        $canAllowance = $user && $user->hasRole('allowance');
        $canRootOrHr = $user && ($user->hasRole('root') || $user->hasRole('hr_access'));
        $today = date('Y-m-d');
    @endphp
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
				Employee Leave
				<small>cuti karyawan</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set("Asia/Jakarta");
						$now=date('Y-m-d');
						$apply=date('Y-m-d',strtotime('-7 days',strtotime($now)));
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
							<i class="fa fa-list"></i>
							<h3 class="box-title" id="judul" style="padding-bottom:25px;">Summary Employee's Leave</h3>
							<div class="pull-right">
								<input type="hidden" id="limit_status" value="{{$limit_leave_status}}">
									@if($canManageLeave)
									<div class="btn-group">
										<button type="button" class="btn btn-primary"><i class="fa fa-list">&nbsp;&nbsp;Action</i></button>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<ul class="dropdown-menu" role="menu">
											<!-- <li>
												<a title="Refresh" href='/Leave/Update'><i class="fa fa-refresh"></i> Refresh Balance</a>
											</li> -->
											<li>
												<a title="Inactive" href='/Leave/Inactive'><i class="fa fa-info"></i> Arsif Leave</a>
											</li>
											<li class="limitUpdate">
												@if($limit_leave_status=='0')
													<a title="Activate" style="cursor:pointer;"><i class="fa fa-lock"></i> Activate Limit</a>
												@else
													<a title="Inactive" style="cursor:pointer;"><i class="fa fa-unlock"></i> Disabled Limit</a>
												@endif
											</li>
											<li class="divider"></li>
											<li>
												&nbsp;
											</li>
										</ul>
									</div>
									@endif
							</div>
						</div>
						<div class="box-body" style="min-height:200px;overflow-x:scroll;">
							<div id="tabel2">
								<table id="tables" class="table table-striped" border="0">
									<thead>
										<tr>
											<th style="width:30px;">NO</th>
											<th>ID.FORM</th>
											<th>NIK</th>
											<th>EMPLOYEE</th>
											<th>DEPT</th>
											<th>JOIN</th>
											<th>START</th>
											<th>END</th>
											<th>+</th>
											<th>-</th>
											<th>TOTAL</th>
											<th>USED</th>
											<th>BAL</th>
										</tr>
									</thead>
									<tbody>
									<?php $no=0;$nik='';$punya=0;$siap=0;$belum=0;$combine='';$qty=0;?>
										@foreach($tb_leave as $dt)
										<?php if($nik!=$dt->NIK||$dt->status=='1'){?>
										<tr <?php if($dt->end<$now)echo "style:background:FF0;";?>>
											<td>
												<?php $no++;echo $no;if($combine==$dt->NIK.'#'.$dt->start){echo " double";$qty++;}$combine=$dt->NIK.'#'.$dt->start;?>
												<div class="pull-rigt">
												<?php
													date_default_timezone_set("Asia/Jakarta");
													$today=date('Y-m-d');
													$tgl1 = new DateTime($dt->join_date);
													$tgl2 = new DateTime($today);
													$diffdays = $tgl2->diff($tgl1)->days;
													$masa_kerja=$diffdays/357;
													if($masa_kerja<1){echo "<span class='label label-danger'>Under 1 Year</span>";$belum++;}
													else{
														if($dt->status=='0'&&request()->user()->hasRole('allowance')){echo "<a href='/Leave/CreateNow/".$dt->id_employee."'><span class='label label-primary'><i class='fa fa-file-o'></i> Create Now</span></a>";$siap++;}
														elseif($dt->status=='0') {echo "<span class='label label-warning'>Inform HR!</span>";$siap++;}
														else {$punya++;}
													}
												?>
												</div>
											</td>
											<td>
												{{$dt->id}}
											</td>
											<td>{{$dt->NIK}}</td>
											<td>{{$dt->employee_name}}</td>
											<td>{{$dt->dept_code}}</td>
											<td><?php echo date('d-m-Y',strtotime($dt->join_date));?></td>
											<td>
												<?php 
													if($dt->start!='')echo date('d-m-Y',strtotime($dt->start));
												?>
											</td>
											<td>
												<?php 
													if($dt->start!='')echo date('d-m-Y',strtotime($dt->end));
												?>
											</td>
											<td><?php echo number_format($dt->sisa,0);?></td>
											<td><?php echo number_format($dt->kurang,0);?></td>
											<td>
												<?php 
												$total=
												$dt->allowance + $dt->sisa - $dt->kurang;
												//echo number_format($total,0);
												echo number_format($dt->allowance,0);
												?>
											</td>
											<td><?php echo number_format($dt->used,0);?></td>
											<td>
												<?php echo number_format($dt->outstanding,0);?>
												<div class="pull-right">
													@if((request()->user()->hasRole('root')||request()->user()->hasRole('hr_access'))&&$dt->outstanding<=0)
														<!-- <a href="/Leave/Create/{{$dt->id}}"><button title="Create New" type="button" class="btn btn-warning btn-xs"><i class="fa fa-file-o"></i></button></a> -->
													@endif
													<?php if(request()->user()->hasRole('allowance')){?>
														<?php if($nik==$dt->NIK){?>
															<a href="/Leave/Employee/Delete/{{$dt->id}}"><button title="Delete" type="button" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button></a>
														<?php }?>
														<?php if($dt->end<=$next_month&&$dt->status=='1'){?>
															<a href="/Leave/Create/{{$dt->id}}"><button title="Create New" type="button" class="btn btn-warning btn-xs"><i class="fa fa-file-o"></i></button></a>
														<?php }?>
														<a href="/Leave/Employee/{{$dt->id}}"><button title="Open" type="button" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></button></a>
													<?php }else{?>
													<?php if(($masa_kerja>=0&&$dt->status=='1')||($dt->isExtend=='1'&&$dt->extend<=$sekarang)){?>
														<a href="/Leave/Employee/{{$dt->id}}"><button title="Open" type="button" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></button></a>
													<?php }}?>
													<?php $nik=$dt->NIK;?>

												</div>
											</td>
										</tr>
										<?php }?>
										@endforeach
										</tbody>
										<tfoot>
											<tr>
												<td>{{$qty}}</td>
												<td colspan="3" style="text-align:center;">
													Under 1 Year : <span class="label label-danger">{{$belum}}</span>
												</td>
												<td colspan="4" style="text-align:center;">
													Need Activation : <span class="label label-info">{{$siap}}</span>
												</td>
												<td colspan="5" style="text-align:center;">
													Activated Form : <span class="label label-success">{{$punya}}</span>
												</td>
											</tr>
										</tfoot>
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
	<script>
		$(document).on('click', '.limitUpdate', function() {
			var limitstatus=document.getElementById('limit_status').value;
			$.ajaxSetup({
                type: "POST",
                url: "/Leave/LimitUpdate",
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                data: {
                    limitstatus: limitstatus,
                },
                success: function(respond) {
					if(respond=='Sukses'){
						window.location.reload();
					}else{
						alert(respond);
					}
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


@endsection
