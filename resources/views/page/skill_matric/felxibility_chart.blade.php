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
		border: 2px solid #999;
        border-bottom: 4px double #999;
        text-align:center;
		/* border-bottom: 2px solid #999; */
		}	
		#table2 td {
		border: 2px solid #999;
		/* border-bottom: 2px solid #999; */
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
    </style>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .vertical-text {
            writing-mode: vertical-rl; /* Teks vertikal kanan-ke-kiri */
            transform: rotate(180deg); /* Putar 180° agar terbaca dari bawah */
            white-space: nowrap;
            height: 350px; /* Sesuaikan tinggi */
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
					<form role="form" action="" method="post">
					<div class="box-header">
						<i class="fa fa-file-text-o"></i>Competence List
						<div class="box-tools pull-right">
                            &nbsp;
                        </div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12 col-md-3 col-lg-2">
                                <div class="box-tools pull-left form-group">
                                    <label>Group Competence</label>
                                    <select class="form-control pilihan" id="select_group">
                                        <option value="{{$data['id_group']}}">{{$data['group_name']}}</option>
                                        @foreach($data['tb_group'] as $dt)
                                            @if($data['id_group']!=$dt->id)
                                                <option value="{{$dt->id}}">{{$dt->group_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
							</div>
							<div class="col-xs-12 col-md-6 col-lg-8">&nbsp;</div>
							<div class="col-xs-12 col-md-3 col-lg-2">
                                <div class="box-tools pull-right form-group">
                                    <label>Group Shift</label>
                                    <select name="select_employee" class="form-control pilihan" id="select_shift">
                                        <option value="{{$data['shift_code']}}">{{$data['shift_code']}}</option>
                                        @foreach($data['tb_shift'] as $dt)
                                            <option value="{{$dt->shift_code}}">{{$dt->shift_code}}</option>
                                        @endforeach
                                    </select>
                                </div>
							</div>
						</div><br>
                        <div class="col-lg-12 col-md-12 col-xs-12">
                            @if($data['employees_qty']>0)
                            <table id="table2" class="table table-border">
                                <thead>
                                    <tr>
                                        <th style="width:40px;vertical-align:middle;">No</th>
                                        <th style="width:120px;vertical-align:middle;">NIK</th>
                                        <th style="width:200px;vertical-align:middle;">EMPLOYEE NAME</th>
                                        @foreach($competences as $competence)
                                            <th class="vertical-text" style="vertical-align:middle;">{{ $competence }}</th>
                                        @endforeach
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php $no=0;?>
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td style="vertical-align:middle;font-weight: bold;"><?php $no++;echo $no;?></td>
                                            <td style="vertical-align:middle;font-weight: bold;">{{ $employee['NIK'] }}</td>
                                            <td style="vertical-align:middle;font-weight: bold;text-align:left;">{{ $employee['employee_name'] }}</td>
                                            @foreach($competences as $competence)
                                                <td>
                                                    <!-- {{ $employee[$competence] ?? '-' }} -->
                                                    <?php
                                                        if($employee[$competence] ==1){$warna='#FF0000';}
                                                        elseif($employee[$competence] ==2){$warna='#FFFF00';}
                                                        elseif($employee[$competence] ==3){$warna='#00FF00';}
                                                        elseif($employee[$competence] ==4){$warna='#0000FF';}
                                                        else {$warna='#FFF';}
                                                    ?>
                                                    <div class="circle" style="
                                                        width: 30px;
                                                        height: 30px;
                                                        background-color: {{$warna}};
                                                        border-radius: 50%;
                                                        display: inline-block;
                                                        border:1px solid #000;
                                                    "></div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						&nbsp;
					</div>
					</form>
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
			'paging'      : false,
			'lengthChange': false,
			'searching'   : false,
			'ordering'    : false,
			'info'        : false,
			"pageLength"  : 500,
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
		$(document).ready(function() {
			var table = $('#tables2').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 500,
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
		$('body').on("change","#select_group",function(){
			var select_group=document.getElementById('select_group').value;
			var select_shift=document.getElementById('select_shift').value;
			window.location.href="{{$site}}/SkillMatric/FelxibilityChart/"+select_group+"/"+select_shift;
		});
	</script>
	
@endsection

