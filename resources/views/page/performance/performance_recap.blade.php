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
				{{$employee}}
				<div class="pull-right">
					<button type="button" class="btn btn-default" onclick="window.location.href='/Performance/{{$periode}}'">Back</button>
				</div>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
		<div class="row">
			<div class="col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-body">
					<table id="tables" class="table table-bordered">
						<thead>
							<tr>
								<th>No</th>
								<th style="text-align:center;font-size:14px;height:20px;">ASPEK NILAN</th>
								<th style="text-align:center;font-size:14px;">JAN-JUN</th>
								<th style="text-align:center;font-size:14px;">JUL-DES</th>
								<th style="text-align:center;font-size:14px;">RATA-RATA</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$no=0;
								$sum=0;
								$sum2=0;
								$sum3=0;
							?>
							@foreach($tb_perform_aspek as $dt)
							<?php 
								$no++;
								$jml=0;
								$isi=0;
								$ave=0;
								$idaspek=$dt->id;
								//if($data[$id_performance.'1'.$idaspek]>0)$isi++;
								if($data[$id_performance.'2'.$idaspek]>0)$isi++;
								//if($data[$id_performance.'3'.$idaspek]>0)$isi++;
								if($data[$id_performance.'4'.$idaspek]>0)$isi++;
								// $jml=$jml+$data[$id_performance.'1'.$idaspek]+$data[$id_performance.'2'.$idaspek]+$data[$id_performance.'3'.$idaspek]+$data[$id_performance.'4'.$idaspek];
								$jml=$jml+$data[$id_performance.'2'.$idaspek]+$data[$id_performance.'4'.$idaspek];
								if($isi>0)$ave=$jml/$isi;
								else $ave=0;
							?>
							<?php
								$sum=$sum+$data[$id_performance.'2'.$idaspek];
								$sum2=$sum2+$data[$id_performance.'4'.$idaspek];
								$ave_sum=($sum+$sum2)/2
							?>
							<tr>
								<td>{{$no}}</td>
								<td style="text-align:left;font-size:14px;vertical-align:top;">{{$dt->nama_aspek}}</td>
								<!-- <td style="text-align:center;font-size:14px;">{{number_format($data[$id_performance.'2'.$idaspek],2)}}</td> -->
								<td style="text-align:center;font-size:14px;">{{number_format($data[$id_performance.'2'.$idaspek],2)}}</td>
								<!-- <td style="text-align:center;font-size:14px;">{{number_format($data[$id_performance.'4'.$idaspek],2)}}</td> -->
								<td style="text-align:center;font-size:14px;">{{number_format($data[$id_performance.'4'.$idaspek],2)}}</td>
								<td style="text-align:center;font-size:14px;">{{number_format($ave,2)}}</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<td colspan="2">&nbsp;</td>
							<td style="text-align:center;">{{$sum}}</td>
							<td style="text-align:center;">{{$sum2}}</td>
							<td style="text-align:center;">({{$sum}}+{{$sum2}})/2=<b>{{number_format($ave_sum,2)}}</b></td>
						</tfoot>
					</table>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->

			</div>
			<!-- /.col -->
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-body">
						<div class="col-xs-12 col-lg-6 col-md-6" style="padding:5px;">
							<div class="form-group">
								<label>Penilai I</label>
								<textarea class="form-control" id="note1" rows="3" placeholder="Enter ..." <?php if($data['pos']!=1)echo "disabled";?>>{{$data['note1']}}</textarea>
							</div>
						</div>
						<div class="col-xs-12 col-lg-5 col-md-5" style="padding:5px;">
							<div class="form-group">
								<label>Penilai II</label>
								<textarea class="form-control" id="note2" rows="3" placeholder="Enter ..." <?php if($data['pos']!=2)echo "disabled";?>>{{$data['note2']}}</textarea>
							</div>
						</div>
						<div class="col-xs-12 col-lg-1 col-md-1" style="padding:30px 5px;">
							@if($data['pos']==1)
								<button type="button" class="btn btn-primary btn-app simpan" data-idperformance="{{$id_performance}}" data-pos="1" data-nama="{{$data['nama1']}}" data-idleader="{{$data['id1']}}"><i class="fa fa-edit"></i> Sign 1</button>
							@elseif($data['pos']==2)
								<button type="button" class="btn btn-primary btn-app simpan" data-idperformance="{{$id_performance}}" data-pos="2" data-nama="{{$data['nama2']}}" data-idleader="{{$data['id2']}}"><i class="fa fa-edit"></i> Sign 2</button>
							@elseif($data['direktur']==1)
								<button type="button" class="btn btn-primary btn-app simpan" data-idperformance="{{$id_performance}}" data-pos="3" data-nama="{{$data['nama_direktur']}}" data-idleader="{{$data['id3']}}"><i class="fa fa-edit"></i> Sign</button>
							@endif
						</div>
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
@section('Modals')
    <div class="modal fade" id="modal-info">
		<div class="modal-dialog box box-primary" style="width:600px;">
			<div class="modal-content">

			</div>
		</div>
	</div>

@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script>
		$('body').on("change","#periode",function(){
			var periode=document.getElementById('periode').value;
			window.location.href="/Performance/"+periode;
		});

	</script>
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
		'paging'      : false,
		'lengthChange': false,
		'searching'   : false,
		'ordering'    : false,
		'info'        : false,
		'autoWidth'   : false
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
			"pageLength"  : 20,
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
		$(document).on('click', '.simpan', function() {
			var idperformance=$(this).data('idperformance');
			var pos=$(this).data('pos');
			var idleader=$(this).data('idleader');
			var nama=$(this).data('nama');
			var note1=document.getElementById('note1').value;
			var note2=document.getElementById('note2').value;
			$.ajaxSetup({
				type:"POST",
				url: "/PerformanceRecap/Save",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			
			
			$.ajax({
				data:{idperformance:idperformance,pos:pos,nama:nama,note1:note1,note2:note2,idleader:idleader},
				success: function(respond){
					// if(respond=="Sukses"){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});

	</script>
  @endsection
