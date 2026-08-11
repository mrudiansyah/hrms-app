
<?php $__env->startSection('Contents'); ?>
   <!-- Contents -->
   <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
		#tables th {
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
            background-color: #d3d8d8ff;
            color: black;
		}	
        .dept-header {
            background-color: #f4f4f4;
            font-weight: bold;
            font-size: 1.1em;
        }
    </style>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Manifest
				<small>Data Manifest <?php echo e($tgl); ?></small>
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
			    <div class="col-xs-12 col-md-8 col-lg-5">
                    <!-- Summary Box -->
                    <?php
                        $total_masuk = 0;
                        $total_tidak_masuk = 0;
                        $total_ijin = 0;
                        $total_tl = 0;
                        $total_status = 0;

                        $data_masuk = [];
                        $data_tidak_masuk = [];
                        $data_ijin = [];
                        $data_tl = [];

                        foreach($manifests as $dept => $employees) {
                            foreach($employees as $dt) {
                                if ($dt->masuk != null) {
                                    $total_masuk++;
                                    $data_masuk[] = $dt;
                                } else {
                                    $total_tidak_masuk++;
                                    $data_tidak_masuk[] = $dt;
                                }

                                if (($dt->ijin != null && trim($dt->ijin) !== '') || ($dt->outside_status == 1 && $dt->referensi == 'Permit')) {
                                    $total_ijin++;
                                    $data_ijin[] = $dt;
                                }
                                if ($dt->tugas_luar == '1' || ($dt->outside_status == 1 && $dt->referensi == 'TL')) {
                                    $total_tl++;
                                    $data_tl[] = $dt;
                                }
                                if ($dt->status == '1') {
                                    $total_status++;
                                }
                            }
                        }
                        $total_sisa = $total_masuk - $total_status;
                    ?>
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-pie-chart"></i> Summary</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="padding: 10px;">
                            <div style="display: flex; width: 100%; text-align: center; border: 1px solid #d2d6de; background: #fff;">
                                <!-- Left Block: TIDAK FINGER / IJIN / TL -->
                                <div style="width: 40%; display: flex; flex-direction: column; border-right: 1px solid #d2d6de;">
                                    <div style="padding: 10px; border-bottom: 1px solid #d2d6de; cursor: pointer;" data-toggle="modal" data-target="#modal-tidak-masuk">
                                        <h4 style="margin: 0; font-weight: bold;" id="summary-total-tidak-finger" class="text-red"><?php echo e($total_tidak_masuk); ?></h4>
                                        <div style="font-size: 11px; font-weight: bold; color: #333;">TIDAK FINGER</div>
                                    </div>
                                    <div style="display: flex; flex-grow: 1;">
                                        <div style="width: 50%; padding: 10px; border-right: 1px solid #d2d6de; cursor: pointer;" data-toggle="modal" data-target="#modal-ijin">
                                            <h4 style="margin: 0; font-weight: bold;" id="summary-total-ijin" class="text-yellow"><?php echo e($total_ijin); ?></h4>
                                            <div style="font-size: 11px; font-weight: bold; color: #333;">IJIN</div>
                                        </div>
                                        <div style="width: 50%; padding: 10px; cursor: pointer;" data-toggle="modal" data-target="#modal-tl">
                                            <h4 style="margin: 0; font-weight: bold;" id="summary-total-tl" class="text-blue"><?php echo e($total_tl); ?></h4>
                                            <div style="font-size: 11px; font-weight: bold; color: #333;">TUGAS LUAR</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Vertical Columns -->
                                <div style="width: 20%; padding: 20px; border-right: 1px solid #d2d6de; display: flex; flex-direction: column; justify-content: center; cursor: pointer;" data-toggle="modal" data-target="#modal-masuk">
                                    <h4 style="margin: 0; font-weight: bold;" id="summary-total-masuk" class="text-green"><?php echo e($total_masuk); ?></h4>
                                    <div style="font-size: 11px; font-weight: bold; color: #333;">MASUK</div>
                                </div>
                                <div style="width: 20%; padding: 20px; border-right: 1px solid #d2d6de; display: flex; flex-direction: column; justify-content: center;">
                                    <h4 style="margin: 0; font-weight: bold;" id="summary-total-ok" class="text-purple" style="color: #605ca8;"><?php echo e($total_status); ?></h4>
                                    <div style="font-size: 11px; font-weight: bold; color: #333;">OK</div>
                                </div>
                                <div style="width: 20%; padding: 20px; display: flex; flex-direction: column; justify-content: center;">
                                    <h4 style="margin: 0; font-weight: bold;" id="summary-total-sisa" class="text-black"><?php echo e($total_sisa); ?></h4>
                                    <div style="font-size: 11px; font-weight: bold; color: #333;">SISA</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Box -->
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Data</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="display: none; background-color: #f8f9fa;">
                            <form action="<?php echo e(url('/manifest')); ?>" method="GET">
                                <div class="row">
                                    <div class="col-md-12 col-lg-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="tgl">Tanggal</label>
                                            <input type="date" name="tgl" id="tgl" class="form-control" value="<?php echo e($tgl); ?>" onchange="this.form.submit()">
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="shift">Shift</label>
                                            <select name="shift" id="shift" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- All Shift --</option>
                                                <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($s); ?>" <?php echo e(request('shift') == $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="dept_code">Dept</label>
                                            <select name="dept_code" id="dept_code" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- All Dept --</option>
                                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($d); ?>" <?php echo e(request('dept_code') == $d ? 'selected' : ''); ?>><?php echo e($d); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="ap">Assembly Point</label>
                                            <select name="ap" id="ap" class="form-control" onchange="this.form.submit()">
                                                <option value="">-- All AP --</option>
                                                <?php $__currentLoopData = $aps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($a); ?>" <?php echo e(request('ap') == $a ? 'selected' : ''); ?>><?php echo e($a); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-6 col-xs-12">
                                        <div class="form-group">
                                            <label for="search_name">Search Name</label>
                                            <div class="input-group">
                                                <input type="text" name="search_name" id="search_name" class="form-control" placeholder="Search by name..." value="<?php echo e($search_name ?? ''); ?>">
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Main Manifest Box -->
                    <div class="box box-primary" style="background:#FFF;">
                        <div class="box-header with-border">
                            <i class="fa fa-users"></i>
                            <h3 class="box-title">Manifest List</h3>
                            
                            <div class="box-tools pull-right">
                                <!-- <a href="<?php echo e(url('/manifest/export-pdf')); ?>?tgl=<?php echo e($tgl); ?>&shift=<?php echo e(request('shift')); ?>&dept_code=<?php echo e(request('dept_code')); ?>&ap=<?php echo e(request('ap')); ?>" class="btn btn-warning btn-sm"><i class="fa fa-file-pdf-o"></i> Download PDF</a> -->
                                <a href="<?php echo e(url('/manifest/sync/' . $tgl)); ?>" class="btn btn-success btn-sm"><i class="fa fa-refresh"></i> Sync Data Manifest</a>
                            </div>
                        </div>
                        <div class="box-body">
                            <?php if(session('success')): ?>
                                <div class="alert alert-success">
                                    <?php echo e(session('success')); ?>

                                </div>
                            <?php endif; ?>
                            <table id="tables" class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <!-- No Department column header here, we use grouped rows -->
                                        <th>No</th>
                                        <th>Employee</th>
                                        <th>Masuk</th>
                                        <th>Keluar</th>
                                        <th>Ijin</th>
                                        <th>TL</th>
                                        <th>Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $manifests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept => $employees): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="dept-header">
                                            <td>&nbsp;</td>
                                            <td><i class="fa fa-building"></i> <?php echo e($dept ?: 'NO DEPARTMENT'); ?></td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?php $no=0;$now=date('Y-m-d H:i:s');?>
                                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $no++;?>
                                        <tr>
                                            <td><?php echo e($no); ?></td>
                                            <td><?php echo e($dt->employee_name); ?><br><?php echo e($dt->NIK); ?></td>
                                            <td>
                                                <?php if($dt->masuk!=null): ?>
                                                    <?php echo e($dt->shift); ?><br>
                                                    <?php echo e(date('H:i',strtotime($dt->masuk))); ?>

                                                <?php else: ?>
                                                    <label style="color:red">Tidak Finger</label>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($now>$dt->check_out): ?>
                                                    <?php if($dt->keluar!=null): ?>
                                                        <br>
                                                        <?php echo e(date('H:i',strtotime($dt->keluar))); ?>

                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo e($dt->ijin); ?>

                                                <?php if($dt->outside_status == 1 && $dt->referensi == 'Permit'): ?>
                                                    <br><span class="label label-warning">Outside</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div style="display:flex; align-items:center;">
                                                    <input type="checkbox" class="tl-checkbox" data-id="<?php echo e($dt->id); ?>" value="1" <?php echo e(($dt->tugas_luar == '1' || ($dt->outside_status == 1 && $dt->referensi == 'TL')) ? 'checked' : ''); ?>>
                                                    <span class="tl-indicator" style="display:none; margin-left:10px;"><i class="fa fa-spinner fa-spin"></i></span>
                                                    <?php if($dt->outside_status == 1 && $dt->referensi == 'TL'): ?>
                                                        <span class="label label-primary" style="margin-left:5px;">TL</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="display:flex; align-items:center;">
                                                    <input type="checkbox" class="status-checkbox" data-id="<?php echo e($dt->id); ?>" value="1" <?php echo e($dt->status == '1' ? 'checked' : ''); ?>>
                                                    <span class="status-indicator" style="display:none; margin-left:10px;"><i class="fa fa-spinner fa-spin"></i></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No data found for <?php echo e($tgl); ?>.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
		</section>
        
        <!-- Modals -->
        <?php $__currentLoopData = [
            ['id' => 'modal-masuk', 'title' => 'Detail Masuk', 'data' => $data_masuk, 'color' => 'green'],
            ['id' => 'modal-tidak-masuk', 'title' => 'Detail Tidak Finger', 'data' => $data_tidak_masuk, 'color' => 'red'],
            ['id' => 'modal-ijin', 'title' => 'Detail Ijin', 'data' => $data_ijin, 'color' => 'yellow'],
            ['id' => 'modal-tl', 'title' => 'Detail Tugas Luar', 'data' => $data_tl, 'color' => 'blue']
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="<?php echo e($modal['id']); ?>" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header bg-<?php echo e($modal['color']); ?>">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:0.8;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="color:#fff;"><?php echo e($modal['title']); ?></h4>
              </div>
              <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Dept</th>
                            <th>Info Absen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $modal['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($idx + 1); ?></td>
                            <td><?php echo e($m->employee_name); ?></td>
                            <td><?php echo e($m->NIK); ?></td>
                            <td><?php echo e($m->dept_code ?: 'NO DEPARTMENT'); ?></td>
                            <td>
                                <?php if($m->masuk): ?>
                                    In: <?php echo e(date('H:i', strtotime($m->masuk))); ?> 
                                    <?php if($m->keluar): ?>
                                        | Out: <?php echo e(date('H:i', strtotime($m->keluar))); ?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-danger">Tidak Finger</span>
                                <?php endif; ?>
                                <?php if($m->ijin): ?>
                                    <br><span class="text-warning"><i class="fa fa-info-circle"></i> Ijin: <?php echo e($m->ijin); ?></span>
                                <?php endif; ?>
                                <?php if($m->tugas_luar == '1'): ?>
                                    <br><span class="text-info"><i class="fa fa-car"></i> Tugas Luar</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if(count($modal['data']) == 0): ?>
                        <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  	</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
	<script>
		$(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.status-checkbox').on('change', function() {
                var cb = $(this);
                var id = cb.data('id');
                var status = cb.is(':checked') ? 1 : 0;
                var indicator = cb.siblings('.status-indicator');

                indicator.show();
                cb.prop('disabled', true);

                $.ajax({
                    url: '<?php echo e(url("/manifest/update-status")); ?>/' + id,
                    type: 'POST',
                    data: {
                        status: status
                    },
                    success: function(response) {
                        indicator.hide();
                        cb.prop('disabled', false);
                        
                        // Dynamic update for status summary (OK and SISA)
                        var currentOk = parseInt($('#summary-total-ok').text());
                        var currentSisa = parseInt($('#summary-total-sisa').text());
                        
                        if (status == 1) {
                            $('#summary-total-ok').text(currentOk + 1);
                            $('#summary-total-sisa').text(currentSisa - 1);
                        } else {
                            $('#summary-total-ok').text(currentOk - 1);
                            $('#summary-total-sisa').text(currentSisa + 1);
                        }
                    },
                    error: function(xhr) {
                        indicator.hide();
                        cb.prop('disabled', false);
                        alert('Something went wrong, please try again.');
                    }
                });
            });

            $('.tl-checkbox').on('change', function() {
                var cb = $(this);
                var id = cb.data('id');
                var tugas_luar = cb.is(':checked') ? 1 : null;
                var indicator = cb.siblings('.tl-indicator');

                indicator.show();
                cb.prop('disabled', true);

                $.ajax({
                    url: '<?php echo e(url("/manifest/update-tl")); ?>/' + id,
                    type: 'POST',
                    data: {
                        tugas_luar: tugas_luar
                    },
                    success: function(response) {
                        indicator.hide();
                        cb.prop('disabled', false);
                    },
                    error: function(xhr) {
                        indicator.hide();
                        cb.prop('disabled', false);
                        alert('Something went wrong, please try again.');
                    }
                });
            });
		} );
		$(document).ready(function() {
			var table = $('#tables').DataTable({
				'paging'      : false,
				'lengthChange': false,
				'searching'   : true,
				'ordering'    : false,
				'info'        : true,
				"pageLength"  : 10,
				'autoWidth'   : false,
				"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]]
			});
		
			new $.fn.dataTable.Buttons( table, {
				buttons: ['copy', 'excel', 'print']
			} );
		
			table.buttons( 0, null ).container().prependTo(
				table.table().container()
			);
		} );
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/manifest/index.blade.php ENDPATH**/ ?>