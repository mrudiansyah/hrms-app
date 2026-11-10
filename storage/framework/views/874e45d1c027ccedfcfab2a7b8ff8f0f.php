

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Audit Log <small>Riwayat perubahan profil karyawan</small></h1>
    </section>

    <section class="content">
        <?php ($activeFilterCount = count(array_filter($filters, fn ($value, $key) => $value !== null && $value !== '' && !($defaultDateRange && in_array($key, ['date_from', 'date_to'], true)), ARRAY_FILTER_USE_BOTH))); ?>
        <div class="audit-toolbar">
            <div>
                <div class="audit-count"><?php echo e(number_format($logs->total())); ?> <span>log tercatat</span></div>
                <div class="audit-filter-summary">
                    <?php if($defaultDateRange): ?>
                        Menampilkan aktivitas 1 bulan terakhir
                        <?php if($activeFilterCount): ?>
                            · <?php echo e($activeFilterCount); ?> filter lainnya aktif
                        <?php endif; ?>
                    <?php elseif($activeFilterCount): ?>
                        <?php echo e($activeFilterCount); ?> filter aktif
                    <?php else: ?>
                        Menampilkan seluruh aktivitas
                    <?php endif; ?>
                </div>
            </div>
            <div class="audit-toolbar-actions">
                <?php if($activeFilterCount): ?>
                    <a href="<?php echo e(url('/AuditLogs')); ?>" class="btn btn-default"><i class="fa fa-times"></i> Reset</a>
                <?php endif; ?>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#auditFilterModal">
                    <i class="fa fa-filter"></i> Filter
                    <?php if($activeFilterCount): ?>
                        <span class="audit-filter-badge"><?php echo e($activeFilterCount); ?></span>
                    <?php endif; ?>
                </button>
            </div>
        </div>

        <div class="modal fade" id="auditFilterModal" tabindex="-1" role="dialog" aria-labelledby="auditFilterTitle">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form method="GET" action="<?php echo e(url('/AuditLogs')); ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="auditFilterTitle"><i class="fa fa-filter"></i> Filter audit log</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nama user</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo e($filters['name'] ?? ''); ?>" maxlength="50" placeholder="Cari nama user">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Aksi</label>
                                        <select name="event" class="form-control">
                                            <option value="">Semua aksi</option>
                                            <?php $__currentLoopData = ['created' => 'Tambah', 'updated' => 'Ubah', 'deleted' => 'Hapus']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>" <?php if(($filters['event'] ?? '') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tabel</label>
                                        <select name="subject_type" class="form-control">
                                            <option value="">Semua tabel</option>
                                            <?php $__currentLoopData = $subjectTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subjectType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($subjectType); ?>" <?php if(($filters['subject_type'] ?? '') === $subjectType): echo 'selected'; endif; ?>><?php echo e($subjectType); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>ID record</label>
                                        <input type="number" name="subject_id" class="form-control" value="<?php echo e($filters['subject_id'] ?? ''); ?>" min="1" placeholder="ID data terkait">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tanggal dari</label>
                                        <input type="date" name="date_from" class="form-control" value="<?php echo e($filters['date_from'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tanggal sampai</label>
                                        <input type="date" name="date_to" class="form-control" value="<?php echo e($filters['date_to'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <input type="text" name="description" class="form-control" value="<?php echo e($filters['description'] ?? ''); ?>" maxlength="255" placeholder="Cari deskripsi">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>IP address</label>
                                        <input type="text" name="ip_address" class="form-control" value="<?php echo e($filters['ip_address'] ?? ''); ?>" maxlength="50" placeholder="Cari IP address">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="<?php echo e(url('/AuditLogs')); ?>" class="btn btn-default pull-left">Reset semua</a>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Terapkan filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="box box-default audit-table-box">
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped audit-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Data</th>
                            <th>Deskripsi</th>
                            <th>Perubahan</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($log->{'created-at'} ? \Illuminate\Support\Carbon::parse($log->{'created-at'})->format('d-m-Y H:i:s') : '-'); ?></td>
                                <td><?php echo e($log->name ?: 'System'); ?></td>
                                <td>
                                    <?php ($eventLabels = ['created' => 'Tambah', 'updated' => 'Ubah', 'deleted' => 'Hapus']); ?>
                                    <span class="label label-<?php echo e($log->event === 'deleted' ? 'danger' : ($log->event === 'created' ? 'success' : 'warning')); ?>">
                                        <?php echo e($eventLabels[$log->event] ?? $log->event); ?>

                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo e($log->subject_type); ?></strong><br>
                                    <small>ID: <?php echo e($log->subject_id ?? '-'); ?></small>
                                </td>
                                <td><?php echo e($log->description); ?></td>
                                <td class="audit-change">
                                    <?php if($log->old_values): ?>
                                        <details>
                                            <summary>Sebelum</summary>
                                            <pre><?php echo e($log->old_values); ?></pre>
                                        </details>
                                    <?php endif; ?>
                                    <?php if($log->new_values): ?>
                                        <details>
                                            <summary>Sesudah</summary>
                                            <pre><?php echo e($log->new_values); ?></pre>
                                        </details>
                                    <?php endif; ?>
                                    <?php if(!$log->old_values && !$log->new_values): ?>
                                        <span class="text-muted">Tidak ada detail</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($log->ip_address); ?>

                                    <?php if($log->user_agent): ?>
                                        <br><small title="<?php echo e($log->user_agent); ?>"><?php echo e($log->user_agent); ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada audit log yang cocok dengan filter.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <?php echo e($logs->links('pagination::bootstrap-4')); ?>

            </div>
        </div>
    </section>

    <style>
        .audit-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
            padding: 16px 20px;
            border: 1px solid #e4eaf1;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(30, 55, 80, .05);
        }
        .audit-count { color: #23364d; font-size: 20px; font-weight: 700; }
        .audit-count span, .audit-filter-summary { color: #738196; font-size: 13px; font-weight: 400; }
        .audit-toolbar-actions { display: flex; align-items: center; gap: 8px; }
        .audit-filter-badge { margin-left: 4px; padding: 2px 6px; border-radius: 10px; background: #fff; color: #337ab7; }
        .audit-table-box { border: 1px solid #e4eaf1; border-radius: 8px; box-shadow: 0 2px 8px rgba(30, 55, 80, .05); }
        .audit-table-box .box-body { padding: 0; }
        .audit-table { margin-bottom: 0; }
        .audit-table th { white-space: nowrap; background: #f7f9fc; color: #46566b; }
        .audit-table td { vertical-align: middle !important; }
        .audit-table-box .box-footer { border-top: 1px solid #edf1f5; background: #fff; }
        .audit-change details { margin-bottom: 5px; }
        .audit-change summary { cursor: pointer; color: #337ab7; }
        .audit-change pre { max-width: 430px; max-height: 220px; overflow: auto; margin: 6px 0; white-space: pre-wrap; word-break: break-word; }
        #auditFilterModal .form-group { margin-bottom: 16px; }
        #auditFilterModal .form-control { border-radius: 4px; }
        @media (max-width: 767px) {
            .audit-toolbar { align-items: flex-start; padding: 14px; }
            .audit-toolbar-actions { flex-shrink: 0; }
        }
    </style>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/audit_logs.blade.php ENDPATH**/ ?>