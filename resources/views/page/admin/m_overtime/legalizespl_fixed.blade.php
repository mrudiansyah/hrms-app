@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   	<meta name="csrf-token" content="{{ csrf_token() }}">
	<style>
	canvas {
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
	</style>

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
				Approval SPL
				<small>Surat Perintah Lembur</small>
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

    <section class="content">
        <?php $grafik=0;if($grafik==1){?>
        <?php if(isset($tb_sumot)){?>
            <div class="box box-success" style="border:0px;">
                <div class="box-header">
                    <i class="fa fa-bar-chart"></i>
                <h3 class="box-title">Overtime vs Sales</h3>
                    <div class="box-tools pull-right">
                        <button title="Edit Sales" type="button" class="sales-modal btn btn-info btn-xs" data-periodesales="{{$periode}}" data-salesammount="{{$salesammount}}"><i class="fa fa-wrench"></i> Sales: <?php echo number_format($salesammount,0);?></button>
                        <a href="/Grafik/{{$periode}}/1" type="button" class="btn btn-primary btn-xs"><i class="fa fa-refresh"></i>&nbsp; Pareto {{$periode}}</a>
                    </div>
                </div>
                <div class="box-body" style="padding:0px 100px 30px 100px;">
        
                    <div style="width: 100%">
                        <canvas id="canvas"></canvas>
                    </div>

                    

                </div>
                <!-- /.box-body -->
            </div>
        <?php }?>
        <?php }?>
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header">
                        <i class="fa fa-list"></i>
                        <h3 class="box-title" id="judul">SPL List</h3>
                        <div class="pull-right">
                            <a  class="btn btn-app table2">
                                <?php if(isset($jmlot_plan)&&$jmlot_plan>0)echo "<span class='badge bg-yellow'>".$jmlot_plan."</span>";?>
                                <i class="fa fa-file-o"></i> New SPL
                            </a>
                            <a  class="btn btn-app" href="{{ route('LegalizeSPLAll') }}">
                                <i class="fa fa-check-square"></i> Approve All
                            </a>
                            <a class="btn btn-app table4">
                                <i class="fa fa-check-square-o"></i> Complete
                            </a>
                        </div>
                    </div>
                    <div class="box-body" style="min-height:200px;overflow-x: scroll;">
                        <div class="col-md-12 table-responsive">
                            <table id="table2" class="table table-striped" border="0">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">Action</th>
                                        <th style="width:60px;">NO</th>
                                        <th style="width:90px;">NO.SPL</th>
                                        <th style="width:90px;">OT.DATE</th>
                                        <th>DEPARTMENT</th>
                                        <th style="width:70px;background:#DDD;">ORDER</th>
                                        <th style="width:70px;background:#DDD;">APPROVED</th>
                                        <th style="width:70px;background:#DDD;">SEEN</th>
                                        <th style="width:70px;background:#DDD;">RECORDED</th>
                                        <th style="width:70px;background:#0F0;">APPROVE</th>
                                        <th style="width:70px;background:#0F0;">PAID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>

            </div>
        </div>
        <!-- /.row -->
    </section>
</div>
@section('Scripts')
<script>
    $(document).ready(function () {
        GetData();
        var table;
        function GetData(){
         table = $("#table2").DataTable({
			processing: true,
			serverSide: true,
			rowReorder: {
				selector: 'td:nth-child(2)'
			},
			responsive: true,
			ajax: {
				type: 'POST',
				url: "{{ route('LegalizeSPL.GetDataLegalize') }}",
				data: function(d) {
					d._token = document.querySelector('meta[name="csrf-token"]')
						.getAttribute('content');
				},
				cache: false,
				dataType: 'json',
                "error": function (err) {
					
				}
			},
			columns: [
						{
							data: 'action'
						},
						{
							data: 'no',
							className: 'text-center'
						},
						
						{
							data: 'id_overtime'
						},
						{
							data: 'ot_date'
						},
						{
							data: 'dept_name',
						},
						{
							data: 'status_diperintah',
						},
						{
							data: 'disetujui',
						},
						{
							data: 'diketahui',
						},
						{
							data: 'status_dicatat',
						},
						{
							data: 'status_approve',
						},
						{
							data: 'status_paid',
						}],
                        // "dom": '<"pull-left"f>rt<"row"<"col-sm-2"l><"col-sm-6"i><"col-sm-4"p>>',

				});
			table.ajax.reload();
        }
            $(".table2").click(function(){
                    table.destroy();

                GetData();
                // $("#table2")
            })
    });
</script>
@endsection
@endsection