
<?php $__env->startSection('Contents'); ?>
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <div class="content-wrapper">
        <section class="content-header">
            <h1 onclick="">
                <?php echo e($juduls); ?>

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
                                <?php $__currentLoopData = $tb_department; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($dt->department!=$department)echo "<option value=".$dt->department.">".$dt->department."</option>";?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                        <input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">	
                                    </div>
                                    <div class="pull-right">
                                        <a href="/TMS/DraftGet/<?php echo e($department); ?>/<?php echo e($periode); ?>" class="btn btn-md btn-primary"><i class="fa fa-refresh"></i> Refresh</a>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table id="tables" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th style="width:120px;">NIK</th>
                                                <th style="width:180px;">Employee name</th>
                                                <th style="width:150px;">Dept</th>
                                                <th>Draft</th>
                                                <th>Suggest</th>
                                                <th>&nbsp;</th>
                                                <?php 
                                                    for($i=1;$i<=31;$i++){
                                                        if(strlen($i)==1)$j='0'.$i;
                                                        else $j=$i;
                                                        $tgl=date('Y-m-d',strtotime($periode.'-'.$j));
                                                        echo "<th style='padding:0px;text-align:center;vertical-align:middle;'>".$j."</th>";
                                                    }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $no=($tb_work_entries_draft->currentPage()-1)*$tb_work_entries_draft->perPage();
                                            ?>
                                            <?php $__currentLoopData = $tb_work_entries_draft; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($dt->plan_actual=='plan'){?>
                                                    <tr>
                                                        <td><?php $no++;echo $no;?></td>
                                                        <td title="<?php echo e($dt->PIN); ?>"><?php echo e($dt->NIK); ?></td>
                                                        <td><?php echo e($dt->nama_karyawan); ?></td>
                                                        <td >
                                                            <?php echo e($dt->department); ?>

                                                        </td>
                                                        <td><?php echo e($dt->id_work_shift); ?></td>
                                                        <td><?php echo e($dt->suggest_ws); ?></td>
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
                                                            echo "<td".$warna." id='no".$no."i".$i."' class='plan_draft' data-konten='no".$no."i".$i."' data-kolom='".$i."' data-idworkentry='".$dt->id."' data-background='".$background."' data-color='".$color."' title='".$plan."'>&nbsp;</td>";

                                                        }?>
                                                    </tr>
                                                <?php }?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>

                                    </table>
                                    <input type="hidden" id="jumlah" value="<?php echo e($no); ?>">
                                    <div class="text-center">
                                        <?php echo e($tb_work_entries_draft->links('pagination::bootstrap-4')); ?>

                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                    
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
	<div class="modal fade" id="modal-edit">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<form>
			<?php echo e(csrf_field()); ?>

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
						<label>group</label>
						<select name="group" class="form-control" id="idworkshift">
							<option value="0"></option>
							<?php $__currentLoopData = $tb_work_shift; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<option value="<?php echo e($dt->id); ?>"><?php echo e($dt->shift_code); ?></option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script>
		$(document).ready(function() {
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			//alert(department)
		});
	</script>
	<script>
		$('body').on("change","#periode",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/TMS/Draft/"+department+"/"+periode;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			window.location.href="<?php echo e($site); ?>/TMS/Draft/"+department+"/"+periode;
		});
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script>
		$(document).ready(function() {
		var table = $('#tables').DataTable({
			'paging'      : false,
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
				url: "<?php echo e($site); ?>/TMS/Shift/Save",
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
					// 	window.location="<?php echo e($site); ?>/updatesTMS/Plan/<?php echo e($department); ?>/<?php echo e($periode); ?>/"+idemployee;
					// }
					window.location="<?php echo e($site); ?>/updatesTMS/Plan/<?php echo e($department); ?>/<?php echo e($periode); ?>/"+idemployee;
				}
			})
			$('#modal-edit').modal('hide');
		});

	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/tms/employee_shift_draft.blade.php ENDPATH**/ ?>