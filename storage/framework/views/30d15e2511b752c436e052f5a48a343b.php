
<?php $__env->startSection('Contents'); ?>
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<div class="pull-left">
							<input type="month" id="periode" class="form-control" value="<?php echo e($periode); ?>">	
						</div>
						<div class="pull-right">
							<button class="btn btn-primary btn-md" id="form" data-idcomponent="" data-periode="<?php echo e($periode); ?>" data-idemployee="" data-amount="0" data-component="" data-description=""><i class="fa fa-plus"></i> &nbsp;Add New</button>
						</div>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered">
							<thead>
								<tr>
									<th style="width:50px;">No</th>
									<th style="width:90px;">NIK</th>
									<th style="width:200px;">EMPLOYEE</th>
									<th style="width:50px;">POSITION</th>
									<th style="width:50px;">DEPT</th>
									<th style="width:50px;">TAX_STATUS</th>
									<th style="width:50px;">PERIOD</th>
									<th style="width:50px;">COMPONENT</th>
									<th style="width:50px;">AMOUNT</th>
									<th>DESCRIPTION</th>
								</tr>
							</thead>
							<tbody>
							<?php $no=0;?>
							<?php $__currentLoopData = $tb_salary_earn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td>
										<?php $no++;?>
										<?php echo e($no); ?>

									</td>
									<td><?php echo e($dt->NIK); ?></td>
									<td><?php echo e($dt->nama_karyawan); ?></td>
									<td><?php echo e($dt->jabatan); ?></td>
									<td><?php echo e($dt->department); ?></td>
									<td><?php echo e($dt->statuspajak); ?></td>
									<td><?php echo e($dt->periode); ?></td>
									<td><?php echo strtoupper($dt->component_name);?></td>
									<td><?php echo number_format($dt->earn_amount,0);?></td>
									<td>
										<?php echo e($dt->description); ?>

										<div class="pull-right">
											<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->nama_karyawan); ?>" data-akses="<?php if($status==1)echo 'Delete';else echo 'Recreate';?>"><i class="<?php if($status==1)echo 'fa fa-trash';else echo 'fa fa-upload';?>"></i></button>
											<button class="btn btn-primary btn-xs" id="form" data-idcomponent="<?php echo e($dt->id); ?>" data-periode="<?php echo e($dt->periode); ?>" data-idemployee="<?php echo e($dt->id_employee); ?>" data-amount="<?php echo number_format($dt->earn_amount,0,",",".");?>" data-componentname="<?php echo e($dt->component_name); ?>" data-description="<?php echo e($dt->description); ?>"><i class="fa fa-edit"></i></button>
										</div>
									</td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>

			</div>
		</div>
	</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Modals'); ?>
	<div class="modal fade" id="modal-form">
		<div class="modal-dialog box box-primary" style="width:350px;">
			<div class="modal-content">
					<form>
					<?php echo e(csrf_field()); ?>

						<div class="modal-header">	
							FORM CONDITIONAL EARN
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="idComponent" class="form-control">
							<div class="form-group opsitambah">
								<label>Employee1</label>
								<!-- <select id="idKaryawan" class="form-control selectpicker" data-live-search="true"> -->
								<select id="idemployeeShow" class="form-control selectpicker" data-live-search="true">
									<option value=""></option>
									<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($dt2->id_employee); ?>"><?php echo e($dt2->nama_karyawan); ?> (<?php echo e($dt2->NIK); ?>)</option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

								</select>
							</div>
							<div class="form-group opsiedit">
								<label>Employee2</label>
								<select id="idemployee" class="form-control" disabled>
									<option value=""></option>
									<?php $__currentLoopData = $tb_employee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($dt2->id_employee); ?>"><?php echo e($dt2->nama_karyawan); ?> (<?php echo e($dt2->NIK); ?>)</option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

								</select>
							</div>
							<div class="form-group">
								<label>Periode</label>
								<input type="month" id="periodeEarn" class="form-control">
							</div>
							<div class="form-group">
								<label>Component</label>
								<!-- <select id="idKaryawan" class="form-control selectpicker" data-live-search="true"> -->
								<select id="componentName" class="form-control">
									<option value="rapel">Rapel</option>
									<option value="insentif_pph21">Insentif PPh21</option>
									<option value="pph21_lebih_bayar">PPh21 Lebih Bayar</option>
								</select>
							</div>
							<div class="form-group">
								<label>Amount</label>
								<input type="text" id="amount" class="form-control" onclick="select();">
							</div>
							<div class="form-group">
								<label>Remark</label>
								<textarea id="description" class="form-control"></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-primary pull-right" id="simpan" data-dismiss="modal">Save</button>
						</div>
					</form>
			</div>

			<!-- /.modal-content -->
		</div>
	<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">
					<b id="aksesText">Delete </b> <b id="delname"></b> ?
					<input type="hidden" id="delid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes, Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<script>
		$('body').on("change","#periode",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			var tipe="<?php echo $tipe;?>";
			window.location.href="<?php echo e($site); ?>/Earn/"+periode+"/"+department+"/0/"+tipe;
		});
		$('body').on("change","#idDepartment",function(){
			var department=document.getElementById('idDepartment').value;
			var periode=document.getElementById('periode').value;
			if(periode=='') var periode=0;
			var tipe="<?php echo $tipe;?>";
			window.location.href="<?php echo e($site); ?>/Earn/"+periode+"/"+department+"/0/"+tipe;
		});
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
 		$(document).on('click', '#form', function() {
			$('#idComponent').val($(this).data('idcomponent'));
			$('#periodeEarn').val($(this).data('periode'));
			$('#idemployee').val($(this).data('idemployee'));
			$('#amount').val($(this).data('amount'));
			$('#componentName').val($(this).data('componentname'));
			$('#description').val($(this).data('description'));
			var kondisi=$(this).data('idemployee');
			if(kondisi>0){
				$('.opsiedit').show();
				$('.opsitambah').hide();
			}else{
				$('.opsiedit').hide();
				$('.opsitambah').show();
			}
			var angka = $('#amount').val();
			formatRupiahS(angka,'amount');
			$('#modal-form').modal('show');
		});
		$(document).on('change', '#idemployeeShow', function() {
			$('#idemployee').val($(this).val());
		});
		$(document).on('keyup', '#amount', function() {
			var biaya = document.getElementById('amount');
			biaya.addEventListener('keyup', function(e){
				biaya.value = formatRupiah(this.value, '');
			});
		});
		$(document).on('click', '#simpan', function() {
			var idcomponent=$('#idComponent').val();
			var periodeearn=$('#periodeEarn').val();
			var idemployee=$('#idemployee').val();
			var amount=$('#amount').val().replace(/\./g,'');
			var componentname=$('#componentName').val();
			var description=$('#description').val();
			var tipe="<?php echo e($tipe); ?>";

			$.ajaxSetup({
				type:"POST",
				url: "<?php echo e($site); ?>/Earn/Simpan",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				data:{idcomponent:idcomponent,periodeearn:periodeearn,idemployee:idemployee,amount:amount,componentname:componentname,description:description,tipe:tipe},
				success: function(respond){
					location.reload();
					// if(respond=='Sukses'){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
				}
			})
		});
		function formatRupiah(angka, prefix){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
 
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
		}
		function formatRupiahS(angka,pemilik){
			var number_string = angka.replace(/[^,\d]/g, '').toString(),
			split   		= number_string.split(','),
			sisa     		= split[0].length % 3,
			rupiah     		= split[0].substr(0, sisa),
			ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
 
			if(ribuan){
				separator = sisa ? '.' : '';
				rupiah += separator + ribuan.join('.');
			}
 
			rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
			$('#'+pemilik).val(rupiah);
		}
		$(document).on('click', '.delete-modal', function() {
			$('#delid').val($(this).data('delid'));
			$('#delname').text($(this).data('delname'));
			$('#aksesText').text($(this).data('akses'));
			$('#modal-delete').modal('show');
		});
		$('.modal-footer').on('click', '.delete', function() {
			var x=$('#delid').val();
			window.location.href='<?php echo e($site); ?>/Earn/Delete/'+x;
		});

	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/payroll', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\payroll\salary_earn.blade.php ENDPATH**/ ?>