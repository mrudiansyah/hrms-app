@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1 onclick="">
				Absence Rate
				
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
					<div class="box-body">
						<div class="row" style="padding:20px;">
							<div class="col-xs-12">
								<div class="box box-rimary box-solid" style="border:0px;">
									<div class="box-header with-border">
										<h3 class="box-title">{{$dept_name}} <?php echo date('F Y',strtotime($Tgl_akhir));?></h3>
										<div class="box-tools pull-right">
											<a href="/AbsensiRate/{{$periode}}/0" class="btn btn-primary">Back</a>
											<!--
											<a href="/Absency/Update/{{$periode}}/0/{{$dept_id}}/{{$hari_kerja}}" class="btn btn-warning">Update</a>
											-->
											<a href="/Absency/Rate/{{$periode}}/{{$dept_id}}/{{$hari_kerja}}" class="btn btn-danger" target="_blank">PDF</a>
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body">
										<table id="tables" class="table table-hover">
											<thead>
												<tr>
													<th style="width:30px;">No</th>
													<th>Employee</th>
													<th style="width:70px;">NIK</th>
													<th style="width:70px;">Plan</th>
													<th style="width:70px;">Act</th>
													<th style="width:70px;">Day</th>
													<th style="width:70px;">Plan</th>
													<th style="width:70px;">Act</th>
													<th style="width:70px;">Hour</th>
													<th style="width:70px;">Late</th>
													<th style="width:70px;">Late(Min)</th>
													<th style="width:70px;">Half</th>
													<th style="width:70px;">Outs</th>
													<th style="width:70px;">Leave</th>
													<th style="width:70px;">Sick</th>
													<th style="width:70px;">Form_A</th>
													<th style="width:70px;">Abcent</th>
													<th style="width:70px;">Total</th>
												</tr>
											</thead>
											<tbody>
											<?php $no=0;?>
											@foreach($tb_absen as $dt)
												<tr>
													<td><?php $no++;echo $no;?></td>
													<td>{{$dt->employee_name}}</td>
													<td>{{$dt->NIK}}</td>
													<td><?php echo number_format($dt->present_plan,0);?></td>
													<td><a href="/Employee/Checktime/{{$dt->id_employee}}/0/{{$Tgl_awal}}/{{$Tgl_akhir}}" target="_blank"><?php echo number_format($dt->present_actual,0);?></a></td>
													<td><?php echo number_format($dt->present_rate,2);?></td>
													<td><?php echo number_format($dt->hour_plan,0);?></td>
													<td><?php echo number_format($dt->hour_actual,0);?></td>
													<td><?php echo number_format($dt->hour_rate,2);?></td>
													<td><?php echo number_format($dt->terlambat,0).' x ';?></td>
													<td><?php echo number_format($dt->terlambat_minutes,0);?></td>
													<td><?php $banyak=$dt->setengah_minutes/240;echo number_format($banyak,0);?></td>
													<td><?php echo number_format($dt->keluar_minutes,0);?></td>
													<td>
														<?php
															$host= mysqli_connect("192.168.1.4","ems","123456","db_ems");
															$query="select * from tb_leaves where id_employee='".$dt->id_employee."' and ((start_leave>='".$Tgl_awal."' and start_leave<='".$Tgl_akhir."')or(finish_leave>='".$Tgl_awal."' and finish_leave<='".$Tgl_akhir."')) and (category='annual' or category='special') and status_legalized=1";
															$qry=mysqli_query($host,$query)or die(mysqli_error($host));
															$c=0;
															$t=$dt->id_employee.': ';
															while($dt2=mysqli_fetch_array($qry)){
																$c=$c+$dt2['leave_count'];
																$t=$t.' ('.$dt2['leave_count'].' on '.$dt2['start_leave'].' ~ '.$dt2['finish_leave'].')';
															}
															//$sum=$off+$tl+$ot;
														?>
														<?php echo number_format($dt->cuti,0);?>
														<div class="pull-right" title="{{$t}}"><?php //if($c!=$dt->cuti)echo "<i class='label label-warning'>Gap ".$c."</i>";?></div>
													</td>
													<td><?php echo number_format($dt->sakit,0);?></td>
													<td><?php echo number_format($dt->izin,0);?></td>
													<td><?php echo number_format($dt->alpa,0);?></td>
													<td>
                                                        <?php $total=$dt->sakit+$dt->izin+$dt->alpa;echo $total;?>
													</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</div>
								</div>

							</div>
						</div>
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


    @if ($message = Session::get('success'))
		<div class="alert alert-info alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-info"></i> Success Alert</h4>
			{{$message}}
		</div>
    @endif
	@if ($errors->any())
		<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
				@if($errors->has('date_off'))
					- Date harus diisi<br>
				@endif
		</div>
	@endif

@endsection
@section('Scripts')
	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/Overtimes/Department/"+periode+"/0";
		});
	</script>
	<!-- Tabel Configuration -->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : true,
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
          'paging'      : false,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 25,
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
    <script>
      $(document).ready(function() {
        var table2 = $('#tables2').DataTable({
          'paging'      : false,
          'lengthChange': false,
          'searching'   : true,
          'ordering'    : true,
          'info'        : true,
          "pageLength"  : 25,
          'autoWidth'   : false,
          "lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
          "scrollX"     : true
        });
      
        new $.fn.dataTable.Buttons( table2, {
          //buttons: ['copy', 'excel', 'print']
			buttons: [
				{ extend: 'copyHtml5', footer: true },
				{ extend: 'excelHtml5', footer: true },
				{ extend: 'print', footer: true }
			]

        } );
      
        table2.buttons( 0, null ).container().prependTo(
          table2.table().container()
        );
      } );


  	</script>

@endsection

