@extends('layouts/memo')
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
				Employee Competence
				<small>&nbsp;</small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-lg-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>Competence Machine Line
						<div class="box-tools pull-right">
                            &nbsp;
                        </div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12 col-md-3 col-lg-2">
                                <div class="box-tools pull-left form-group">
									<label>Line</label>
									<select name="select_employee" class="form-control pilihan" id="resource_group">
										@if($data['resource_group']==0)
											<option value="">Select Line</option>
										@else
											<option value="{{$data['resource_group']}}">{{$data['resource_group']}}</option>
										@endif
										@foreach($data['tb_machine_list'] as $dt)
											<option value="{{$dt->resource_group_id}}">{{$dt->resource_group_id}}</option>
										@endforeach
									</select>
                                </div>
							</div>
							<div class="col-xs-12 col-md-6 col-lg-8">&nbsp;</div>
							<div class="col-xs-12 col-md-3 col-lg-2">
                                <div class="box-tools pull-right form-group">
									<label>Shift/Group</label>
									<select name="select_employee" class="form-control pilihan" id="shift_code">
										<option value="{{$data['shift_code']}}">{{$data['shift_code']}}</option>
										@foreach($data['tb_shift_list'] as $dt)
											<option value="{{$dt->shift_code}}">{{$dt->shift_code}}</option>
										@endforeach
									</select>
                                </div>
							</div>
						</div><br>
						<div class="row">
                            @foreach($data['tb_machine'] as $dt)
                            <?php
                                if($dt->actual_level==1){$tatus='danger';$warna='#FF0000';}
                                elseif($dt->actual_level==2){$status='warning';$warna='#FFFF00';}
                                elseif($dt->actual_level==3){$status='success';$warna='#00FF00';}
                                elseif($dt->actual_level==4){$status='primary';$warna='#0000FF';}
                                else {$status='default';$warna='#FFF';}
                            ?>
							<div class="col-xs-12 col-md-4 col-lg-2">
                                <div class="box box-{{$status}}" style="border:1px solid #000;">
                                    <div class="box-header" style="text-align:center;<?php if($status=='yellow')echo 'color:#000;';?>"><b>{{$dt->resource_label}}</b></div>
                                    <div class="box-body">
										@if($dt->photo!='')
                                        	<img class="profile-user-img img-responsive img-square" style="width:130px;height:160px;border:1px solid #DDD;" src="<?php echo $dt->photo;?>" alt="User profile picture">
										@else
											<div class="profile-user-img img-responsive img-square"  style="width:130px; height:160px; border:1px solid #DDD;">&nbsp;</div>
                                        @endif
										<br>
                                        <table style="width:100%;">
                                            <tr><td>NIK</td><td>:</td><td style="text-align:right;">{{$dt->NIK}}</td></tr>
                                            <tr><td>Name</td><td>:</td><td style="text-align:right;">{{$dt->employee_name}}</td></tr>
                                            <tr><td>Level</td><td>:</td><td style="text-align:right;">{{$dt->actual_level}}</td></tr>
                                        </table>
                                    </div>
                                    <div style="text-align: center; width: 100%;">
                                    <div class="circle" style="
                                        width: 60px;
                                        height: 60px;
                                        background-color: {{$warna}};
                                        border-radius: 50%;
                                        display: inline-block;
                                        border:1px solid #000;
                                    "></div>
                                    </div>
                                </div>
							</div>
                            @endforeach
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

@endsection
@section('Scripts')
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
	<script>
		$('body').on("change",".pilihan",function(){
			var resource_group=document.getElementById('resource_group').value;
			var shift_code=document.getElementById('shift_code').value;
			window.location.href="{{$site}}/SkillMatric/LineEmployee/"+resource_group+"/"+shift_code;
		});
	</script>
	
@endsection

