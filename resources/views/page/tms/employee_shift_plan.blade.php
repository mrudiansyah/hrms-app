@extends('layouts/admin')
@section('Contents')
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="content-wrapper">
        <section class="content-header">
            <h1 onclick="">
                {{$juduls}}
                <small><?php if(isset($subjudul))echo $subjudul;else echo 'TMS';?></small>
                <div class="pull-right">
                    <form role="search">
                        <div class="form-group">
                            <select id="idDepartment" class="form-control">
                                <?php 
                                if($department!=0){
                                    echo "<option value=".$department.">".$department."</option>";
                                    echo "<option value='0'>ALL DEPARTMENT</option>";
                                }
                                else echo "<option value='0'>ALL DEPARTMENT</option>";
                                ?>
                                <?php if($juduls!='Contract Compenastion'&&$juduls!='Tax Calculation (Compensation)'&&$juduls!='Group Shift'){?>
                                @foreach($tb_department as $dt)
                                <?php if($dt->department!=$department)echo "<option value=".$dt->department.">".$dt->department."</option>";?>
                                @endforeach
                                <?php }?>
                            </select>
                        </div>
                    </form>
                </div>
            </h1>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary" style="background:#FFF;">
                        <div class="box-body">
                            <div class="col-xs-12">
                                <div class="box-header">
                                    &nbsp;
                                    <div class="pull-left">
                                        <input type="month" id="periode" class="form-control" value="{{$periode}}" min="2025-01">	
                                    </div>
                                    <div class="pull-right">
                                        <?php foreach($tb_work_time as $dt){
                                            if($shift==$dt->id)$warna=" style='background:".$dt->background.";color:".$dt->color.";border:2px solid #000;'";
                                            else $warna=" style='background:".$dt->background.";color:".$dt->color.";'";
                                            if($dt->isactive==1){?>
                                                <a href="{{$site}}/TMS/Plan/{{$department}}/{{$periode}}/{{$dt->id}}/{{$group}}" class="btn btn-default btn-xs"<?php echo $warna;?>>
                                                    <?php
                                                        echo '<b>'.$dt->id.'</b> '.substr($dt->check_in,0,5).'-'.substr($dt->check_out,0,5);
                                                    ?>
                                                </a>
                                        <?php }}?>
                                        <br>
                                        <a href="{{$site}}/TMS/Plan/{{$department}}/{{$periode}}/0/0" class="btn btn-default btn-xs"style='background:white;color:black;'>
                                            &nbsp;OFF / Free&nbsp;
                                        </a>
                                        <!--
                                            <a href="{{$site}}/updatesTMS/Plan/{{$department}}/{{$mulai}}" class="btn btn-default btn-md"><i class="fa fa-download"></i> &nbsp;Schedule</a>
                                        -->
                                        @if (request()->user()->hasRole('root')||request()->user()->hasRole('tms'))
                                            <!-- <a href="{{$site}}/TMSSetup/{{$department}}/{{$periode}}" class="btn btn-default btn-md"><i class="fa fa-calendar"></i> &nbsp;Generate</a> -->
                                        @endif
                                    </div>
                                </div>
                                @if (request()->user()->hasRole('root')||request()->user()->hasRole('tms')||request()->user()->hasRole('admin_department'))
                                    <div class="box-body">
                                        <div class="pull-left">
                                            <div class="form-group">
                                                <label>Group/Shift</label>
                                                <select class="form-control" id="selectidworkshift">
                                                    <?php 
                                                    if($group!=0){
                                                        echo "<option value=".$group.">".$shift_code."</option>";
                                                        echo "<option value='0'>ALL GROUP</option>";
                                                    }
                                                    else echo "<option value='0'>ALL GROUP</option>";
                                                    ?>
                                                    @foreach($tb_work_shift as $dt)
                                                        <?php if($dt->id!=$group)echo "<option value='".$dt->id."'>".$dt->shift_code."</option>";?>
                                                        
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="pull-right">
                                            @if($jumlah>0)
                                                <a href="{{$site}}/TMS/{{$department}}/{{$periode}}/0" class="btn btn-primary btn-md"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;Work Entry</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="box-body">
                                    <table id="table2" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th style="width:120px;">NIK</th>
                                                <th style="width:180px;">Employee name</th>
                                                <th style="width:140px;">Dept</th>
                                                <th style="width:80px;">Group</th>
                                                <th>&nbsp;</th>
                                                <?php 
                                                    for($i=1;$i<=31;$i++){
                                                        if(strlen($i)==1)$j='0'.$i;
                                                        else $j=$i;
                                                        $tgl=date('Y-m-d',strtotime($periode.'-'.$j));
                                                        if($group>0&&(request()->user()->hasRole('root')||request()->user()->hasRole('tms')))
														echo "<th style='padding:0px;text-align:center;vertical-align:middle;cursor:pointer;'><a style='cursor:pointer !important;' href='".$site."/updateTMS/Plan/".$department."/".$tgl."/".$shift."/".$group."'>".$j."</a></th>";
                                                        else 
														echo "<th style='padding:0px;text-align:center;vertical-align:middle;'>".$j."</th>";
                                                    }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $no=0;	
                                                $selectedWorkTime=$work_time_lookup->get($shift);
                                                $background_select=$selectedWorkTime->background ?? '#FFFFFF';
                                            ?>
                                            @foreach($tb_work_entries as $dt)
                                                <?php if($dt->plan_actual=='plan'){?>
                                                    <tr>
                                                        <td><?php $no++;echo $no;?></td>
                                                        <td title="{{$dt->PIN}}">{{$dt->NIK}}</td>
                                                        <td>{{$dt->nama_karyawan}}</td>
                                                        <td >
                                                            {{$dt->department}}
                                                            <div class="pull-right">
                                                                <?php //if($dt->id_work_shift!=$dt->suggest_ws)echo "Check";?>
                                                            </div>
                                                        </td>
                                                        <td>{{$dt->shift_code}}</td>
                                                        <td >
                                                            <?php echo "<button type='button' class='pull-right editcycle btn btn-primary btn-xs' data-idcontract='".$dt->id_contract."' data-idworkshift='".$dt->id_work_shift."' data-idemployee='".$dt->id_employee."' data-employeename='".$dt->nama_karyawan."'><i class='fa fa-edit'></i></button>";?>
                                                        </td>
                                                        <?php 
                                                        for($i=1;$i<=31;$i++){
                                                            if(strlen($i)==1)$j='D0'.$i;
                                                            else $j='D'.$i;
                                                            $plan=$dt->$j;
                                                            $workTime=$work_time_lookup->get($plan);
                                                            $background=$workTime->background ?? '#FFFFFF';
                                                            $color=$workTime->color ?? '#FFFFFF';
                                                            $warna=" style='background:".$background.";color:".$color.";'";
                                                            echo "<td".$warna." id='no".$no."i".$i."' class='plan' data-konten='no".$no."i".$i."' data-kolom='".$i."' data-idworkentry='".$dt->id."' data-background='".$background."' data-color='".$color."' title='".$plan."'>&nbsp;</td>";

                                                        }?>
                                                    </tr>
                                                <?php }?>
                                            @endforeach
                                        </tbody>

                                    </table>
                                    <input type="hidden" id="jumlah" value="{{$no}}">
                                </div>
                                <!-- /.box-body -->
                            </div>
                    
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('Modals')
	<div class="modal fade" id="modal-edit">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<form>
			{{ csrf_field() }}
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="id_employee" id="idemployee">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="judul"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Group</label>
						<select name="group" class="form-control" id="idworkshift">
							<option value="0"></option>
							@foreach($tb_work_shift as $dt)
								<option value="{{$dt->id}}">{{$dt->shift_code}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary savecycle pull-left">Simpan</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			</form>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
@endsection
@section('Scripts')
	<script type="text/javascript">
		$('body').on("change","#periode",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			var shift="<?php echo $shift;?>";
			var group="<?php echo $group;?>";
			window.location.href="{{$site}}/TMS/Plan/"+department+"/"+periode+"/"+shift+"/"+group;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			var shift="<?php echo $shift;?>";
			var group="<?php echo $group;?>";
			window.location.href="{{$site}}/TMS/Plan/"+department+"/"+periode+"/"+shift+"/"+group;
		});
		$('body').on("change","#selectidworkshift",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			var shift="<?php echo $shift;?>";
			var group=document.getElementById('selectidworkshift').value;
			if(group=='')var group=0;
			window.location.href="{{$site}}/TMS/Plan/"+department+"/"+periode+"/"+shift+"/"+group;
		});
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		$(document).on('click', '.editcycle', function() {
			$('#id').val($(this).data('idcontract'));
			$('#idworkshift').val($(this).data('idworkshift'));
			$('#idemployee').val($(this).data('idemployee'));
			$('#judul').text($(this).data('employeename'));
			$('#modal-edit').modal('show');
		});
		$(document).on('click', '.savecycle', function() {
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/TMS/Shift/Save",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var id=$('#id').val();
			var idworkshift=$('#idworkshift').val();
			var idemployee=$('#idemployee').val();
			$.ajax({
				data:{id:id,idworkshift:idworkshift,idemployee:idemployee},
				success: function(respond){
					// if(respond=='Sukses'){
					// 	window.location="{{$site}}/updatesTMS/Plan/{{$department}}/{{$periode}}/"+idemployee;
					// }
					window.location="{{$site}}/updatesTMS/Plan/{{$department}}/{{$periode}}/"+idemployee;
				}
			})
			$('#modal-edit').modal('hide');
		});
	</script>
	<script>
		$(document).ready(function() {
		var table = $('#tables').DataTable({
			'paging'      : true,
			'lengthChange': false,
			'searching'   : true,
			'ordering'    : false,
			'info'        : true,
			"pageLength"  : 50,
			'autoWidth'   : false,
			"lengthMenu"  : [[5,10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
			//"scrollX"     : true
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
		$(document).on('click', '.plan', function() {
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/updateTMS/Plan",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var idcell=$(this).data('konten');
			var idshift="<?php echo $shift;?>";
			var idworkentry=$(this).data('idworkentry');
			var kolom=$(this).data('kolom');

			var background="{{$background_select}}";

			if(idshift!=100){

				$.ajax({
					data:{idworkentry:idworkentry,idshift:idshift,kolom:kolom},
					success: function(respond){
						// //alert(respond);
						// if(respond=='<html><body><p>Sukses</p></body></html>'){
						// 	document.getElementById(idcell).style.backgroundColor= background;
						// 	document.getElementById(idcell).style.fontColor= background;
						// 	//$("#"+idcell).html(respond);
						// }else{
						// 	alert("Gagal");
						// }
						document.getElementById(idcell).style.backgroundColor= background;
						document.getElementById(idcell).style.fontColor= background;
					}
				})

			}
		});
	</script>
@endsection
