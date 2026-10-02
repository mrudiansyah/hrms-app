@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <?php $background_select='#cccccc';?>
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
                <div class="col-xs-12 col-lg-4 col-md-4">
                    <div class="box box-primary" style="background:#FFF;">
                        <div class="box-header">
                            <lable style="font-size:21px;">Daily Presence</lable>
                            <div class="pull-right">
                                <input type="date" id="tanggal" class="form-control" value="{{$tanggal}}" min="2025-01-01">
                            </div>
                        </div>
                        <div class="box-body">
                            <table id="table1" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Color</th>
                                        <th>Category</th>
                                        <th>
                                            Employee
                                            <div class="pull-right">
                                                <a href="/DailyPresence/{{$department}}/{{$tanggal}}/0" title="Info"><i class="fa fa-user"></i></a>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no=0;$qty=0;?>
                                    @foreach($tb_work_entry as $dt)
                                        <?php $no++;$qty=$qty+$dt->employee_count;?>
                                        <?php
                                            $nilai_kolom=$dt->$kolom;
                                            $workCode=$work_code_lookup->get($nilai_kolom);
                                            $source_check=$workCode->source_check ?? '';
                                            $background=$workCode->background ?? '#000';
                                        ?>
                                        <tr>
                                            <td style="width:30px;">{{$no}}</td>
                                            <td style="width:90px;"><small class="label label-default" style="background:{{$background}}">{{$background}}</small></td>
                                            <td>{{$source_check}}</td>
                                            <td>
                                                {{$dt->employee_count}}
                                                <div class="pull-right">
                                                    <a href="/DailyPresence/{{$department}}/{{$tanggal}}/{{$nilai_kolom}}" title="Info"><i class="fa fa-user"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>Total</td>
                                        <td>
                                            <label class="label bg-blue">{{$qty}}</label>
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <div class="col-xs-12 col-lg-8 col-md-8">
                    <div class="box box-primary" style="background:#FFF;">
                        <div class="box-header">
                            <lable style="font-size:21px;">Daily Presence</lable>
                            <div class="pull-right">
								&nbsp;
							</div>
                        </div>
                        <div class="box-body">
                            @if($data['capture']==0)
                                <p>Data belum tersedia, proses capture belum dilakukan atau masih on progress</p>
                            @else
                                <table id="tables" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th style="width:120px;">NIK</th>
                                            <th style="width:180px;">Employee name</th>
                                            <th style="width:90px;">Dept</th>
                                            <th style="width:60px;">Status</th>
                                            <th>Ketarangan</th>
                                            <th style="width:150px;">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $no=0;
                                            $w5d=0;
                                            $w6d=0;
                                        ?>
                                        @foreach($tb_work_entries as $dt)
                                            <?php 
                                                if($dt->plan_actual=='actual'){
                                                    // $x=strlen($dt->$kolom);
                                                    // if($x>=3)$y=substr($dt->$kolom,0,2);
                                                    // else $y=$dt->$kolom;
                                            ?>
                                                @if($code==$dt->$kolom||$code==100)
                                                <tr>
                                                    <td>
                                                        <?php $no++;echo $no;?>
                                                    </td>
                                                    <td title="{{$dt->PIN}}">{{$dt->NIK}}</td>
                                                    <td>{{$dt->nama_karyawan}}</td>
                                                    <td >{{$dt->department}}</td>
                                                        <?php 
                                                            $j=$kolom;
                                                            $act=$dt->$j;

                                                            $status='';
                                                            $remark='';
                                                            $background="#000";
                                                            $workCode=$work_code_lookup->get($act);
                                                            $source_check=$workCode->source_check ?? '';
                                                            $background=$workCode->background ?? '#000';
                                                            $category=$workCode->category ?? '';
                                                            
                                                        ?>
                                                    <td><?php echo "<small class='label label-default' title='".$category."' style='background:".$background."'>".$source_check."</small>";?></td>
                                                    <td>
                                                        <?php 
                                                            $source_check2=$source_check;
                                                            $background2=$background;
                                                            $checktime=$daily_checktime_lookup->get($dt->id);
                                                            if($checktime){
                                                                $checktimeCode=$work_code_lookup->get($checktime->entry_code);
                                                                $source_check2=$checktimeCode->source_check ?? '';
                                                                if($checktime->status==1){
                                                                    $background2=$checktimeCode->background ?? $background;
                                                                }else{
                                                                    $background2="#cccccc";
                                                                }
                                                                $remark=$checktime->remark;
                                                                $info=$checktime->info;
                                                            }
                                                            if($source_check!=$source_check2){
                                                                if($source_check2=='Form BCD'){
                                                                    echo "<small class='label label-default' style='background:".$background2."'>".$info."</small>";
                                                                }else{
                                                                    echo "<small class='label label-default' style='background:".$background2."'>".$source_check2."</small>";
                                                                }													
                                                            }
                                                        ?>
                                                        &nbsp;
                                                        @if($source_check=='Alpha')
                                                            {{$remark}}
                                                        @endif
                                                        
                                                    </td>
                                                    <td>
                                                    <div class="pull-right">
                                                            @if($source_check=='Alpha'||$source_check=='Come Late'||$source_check=='Late Upload'||$source_check=='Come Late Night'||$source_check=='Late Upload Night'||$source_check=='Leave'||$source_check=='Freeday')
                                                                <div class="pull-right">
                                                                    @if($lock_status==0)
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-primary"><i class="fa fa-list"></i>&nbsp;&nbsp;Reconcile</button>
                                                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                                            <span class="caret"></span>
                                                                            <span class="sr-only">Toggle Dropdown</span>
                                                                        </button>
                                                                        <ul class="dropdown-menu" role="menu">
                                                                            <li>
                                                                                <a class="reconsile" data-source="leave" data-idwe="{{$dt->id}}" data-id_employee="{{$dt->id_employee}}" data-tgl="{{$tanggal}}"><i>Leave</i></a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="reconsile" data-source="skd" data-idwe="{{$dt->id}}" data-id_employee="{{$dt->id_employee}}" data-tgl="{{$tanggal}}"><i>SKD</i></a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="reconsile" data-source="ijin" data-idwe="{{$dt->id}}" data-id_employee="{{$dt->id_employee}}" data-tgl="{{$tanggal}}"><i>Form A</i></a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="reconsile" data-source="permit_b" data-idwe="{{$dt->id}}" data-id_employee="{{$dt->id_employee}}" data-tgl="{{$tanggal}}"><i>Form B</i></a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="reconsile" data-source="permit_c" data-idwe="{{$dt->id}}" data-id_employee="{{$dt->id_employee}}" data-tgl="{{$tanggal}}"><i>Form C</i></a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="reconsile" data-source="permit_d" data-idwe="{{$dt->id}}" data-id_employee="{{$dt->id_employee}}" data-tgl="{{$tanggal}}"><i>Form D</i></a>
                                                                            </li>
                                                                            <li class="divider"></li>
                                                                            <li>
                                                                                <a href="/TMSChecktime/{{$dt->id_employee}}/{{$periode}}" target="_blank">Check Finger</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="/TMS/UpdateOne/{{$periode}}/{{$dt->id_employee}}/{{$tanggal}}">Refresh Status</a>
                                                                            </li>
                                                                            @if (request()->user()->hasRole('hr_access'))
                                                                                <li>
                                                                                    <a class='custom-modal' data-idworkentry='{{$dt->id}}' data-namakaryawan='{{$dt->nama_karyawan}}' data-idcolumn='{{$j}}' data-pin="{{$dt->PIN}}" data-nik="{{$dt->NIK}}">Update Finger</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class='checktime-modal' data-idworkentry='{{$dt->id}}' data-namakaryawan='{{$dt->nama_karyawan}}' data-idcolumn='{{$j}}'>Update Status</a>
                                                                                </li>
                                                                            @else
                                                                                <li>
                                                                                    <a class='custom-modal' data-idworkentry='{{$dt->id}}' data-namakaryawan='{{$dt->nama_karyawan}}' data-idcolumn='{{$j}}' data-pin="{{$dt->PIN}}" data-nik="{{$dt->NIK}}">Gagal Finger</a>
                                                                                </li>
                                                                            @endif
                                                                            
                                                                        </ul>
                                                                    </div>
                                                                    @endif
                                                                </div>																	
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endif
                                            <?php }?>
                                        @endforeach
                                    </tbody>

                                </table>
                            @endif
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('Modals')
	<div class="modal fade" id="modal-checktime">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Reconcile Work Entry</h4>
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="{{ csrf_token() }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="form-group">
								<label>Nama</label>
								<input type="hidden" id="idworkentry" class="form-control">
								<input type="hidden" id="idcolumn" class="form-control">
								<input type="text" id="namakaryawan" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Checktime Source</label>
								<select name="group" class="form-control" id="entrycode">
									<option value=""></option>
									@foreach($tb_work_code as $dt)
										<option value="{{$dt->work_code}}">{{$dt->source_check}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label>Remark</label>
								<textarea class="form-control" id="remark"></textarea>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left simpan" data-dismiss="modal">Save</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-custom">
		<div class="modal-dialog box box-primary" style="width:300px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Update Finger (Gagal Finger)</h4>
				</div>
				<div class="modal-body">
					<form role="form" action="" method="post">
						<meta name="csrf-token" content="{{ csrf_token() }}">
						{{ csrf_field() }}
						<div class="box-body">
							<div class="form-group">
								<label>Nama</label>
								<input type="text" id="employeename" class="form-control">
								<input type="hidden" id="idworkentry2" class="form-control">
								<input type="hidden" id="idcolumn2" class="form-control">
								<input type="hidden" id="pin" class="form-control">
								<input type="hidden" id="nik" class="form-control">
								<input type="hidden" id="status" value="WFO" class="form-control">
								<input type="hidden" id="statusact" value="IN" class="form-control">
							</div>
							<div class="form-group">
								<label>Actual Checktime</label>
								@if (request()->user()->hasRole('hr_access'))
									<input type="hidden" name="actual_checktime_draft" id="actualchecktime_draft" class="form-control" value="">
									<input type="datetime-local" name="actual_checktime" id="actualchecktime" class="form-control">
								@else
								<input type="hidden" name="actual_checktime" id="actualchecktime" class="form-control" value="">
									<input type="datetime-local" name="actual_checktime_draft" id="actualchecktime_draft" class="form-control">
								@endif
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary pull-left" id="kirim" data-dismiss="modal">Save</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-loading">
		<div class="modal-dialog box box-info" style="padding:0;margin:0;height:100%;width:100%;opacity: 0;">
			<div class="modal-content">
				<div class="box-body">
					Proses Update, Mohon Tunggu....!
				</div>
				<div class="overlay">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
	</div>

@endsection
@section('Scripts')
	<script type="text/javascript">
		$('body').on("change","#tanggal",function(){
			var department="{{$department}}";
			var tanggal=document.getElementById('tanggal').value;
			if(periode=='') var periode=0;
			var code="{{$code}}";
			window.location.href="{{$site}}/DailyPresence/"+department+"/"+tanggal+"/"+code;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var tanggal="{{$tanggal}}";
			if(periode=='') var periode=0;
			var code="{{$code}}";
			window.location.href="{{$site}}/DailyPresence/"+department+"/"+tanggal+"/"+code;
		});
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
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
	<script type="text/javascript">
		$(document).on('click', '.checktime-modal', function() {
			$('#idworkentry').val($(this).data('idworkentry'));
			$('#idcolumn').val($(this).data('idcolumn'));
			$('#namakaryawan').val($(this).data('namakaryawan'));
			$('#modal-checktime').modal('show');
		});
		$('.modal-footer').on('click', '.simpan', function() {
			var idworkentry=$('#idworkentry').val();
			var idcolumn=$('#idcolumn').val();
			var entrycode=$('#entrycode').val();
			var remark=$('#remark').val();

			var background="{{$background_select}}";
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/updateTMS/Actual",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.ajax({
				data:{idworkentry:idworkentry,idcolumn:idcolumn,entrycode:entrycode,remark:remark},
				success: function(respond){
					// if(respond=='Sukses'){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});
		$(document).on('click', '.reconsile', function() {
			var source=$(this).data('source');
			var id_employee=$(this).data('id_employee');
			var tgl=$(this).data('tgl');
			var idwe=$(this).data('idwe');
			$.ajaxSetup({
				type:"POST",
				url: "{{$site}}/TMS/Reconsile",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.ajax({
				data:{source:source,id_employee:id_employee,tgl:tgl,idwe:idwe},
				success: function(respond){
					const teks = respond;
					location.reload();
				}
			})
		});
		$(document).on('click', '.custom-modal', function() {
			$('#idworkentry2').val($(this).data('idworkentry'));
			$('#idcolumn2').val($(this).data('idcolumn'));
			$('#employeename').val($(this).data('namakaryawan'));
			$('#pin').val($(this).data('pin'));
			$('#nik').val($(this).data('nik'));
			$('#modal-custom').modal('show');
		});
		$("#kirim").click(function(){
			$('#modal-loading').modal('show');
			$.ajaxSetup({
				type:"POST",
				url: "/TMS/SaveManual",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			var checktime=$("#actualchecktime").val();
			var checktime_draft=$("#actualchecktime_draft").val();
			var nik=$("#nik").val();
			var employeename=$("#employeename").val();
			var pin=$("#pin").val();
			var status=$("#status").val();
			var statusct=$("#statusact").val();
			var idworkentry2=$('#idworkentry2').val();
			var idcolumn2=$('#idcolumn2').val();
			//alert(idworkentry2+idcolumn2);
			alert("Menyimpan Data, Mohon tunggu sampai ada notif Selesai");

			if((checktime==''&&checktime_draft=='')||nik==''||pin==''||status==''){
				alert('Data belum lengkap');
			}else{
				$.ajax({
					data:{idworkentry2:idworkentry2,idcolumn2:idcolumn2,PIN:pin,checktime:checktime,checktime_draft:checktime_draft,employee_name:employeename,NIK:nik,status_kerja:status,status_checktime:statusct},
					success: function(respond){
						alert('Selesai');
						location.reload();
						// alert(respond);
					}
				})
			}
		});

	</script>
@endsection
