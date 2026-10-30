

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Event Participant</h1>
    </section>

    <section class="content">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul class="list-unstyled" style="margin-bottom:0;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo e($event ? 'Edit Event' : 'Buat Event'); ?></h3>
                        <?php if($event): ?>
                            <div class="box-tools pull-right">
                                <a href="/Event/0" class="btn btn-default btn-xs">Event Baru</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="/Event/Save">
                        <?php echo csrf_field(); ?>
                        <?php if($event): ?>
                            <input type="hidden" name="id" value="<?php echo e($event->id); ?>">
                            <input type="hidden" name="id_training_schedule" value="<?php echo e($event->id_training_schedule); ?>">
                        <?php endif; ?>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="schedule">Training Schedule (opsional)</label>
                                <select id="schedule" name="id_training_schedule" class="form-control"
                                    <?php echo e($event ? 'disabled' : ''); ?>>
                                    <option value="">Isi event secara manual</option>
                                    <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($schedule->id); ?>"
                                            data-name="<?php echo e($schedule->training_name); ?>"
                                            data-date="<?php echo e($schedule->tanggal); ?>"
                                            data-start="<?php echo e($schedule->start); ?>"
                                            data-finish="<?php echo e($schedule->finish); ?>"
                                            <?php echo e(old('id_training_schedule', $event->id_training_schedule ?? '') == $schedule->id ? 'selected' : ''); ?>>
                                            <?php echo e($schedule->tanggal); ?> - <?php echo e($schedule->training_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php if($event): ?>
                                    <small class="text-muted">Jadwal tidak dapat diubah setelah event dibuat.</small>
                                <?php endif; ?>
                            </div>

                            <?php ($trainingEvent = $event && $event->id_training_schedule); ?>
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" name="category" class="form-control"
                                    <?php echo e($trainingEvent ? 'disabled' : ''); ?>>
                                    <option value="Training" <?php echo e(old('category', $event->category ?? 'Training') === 'Training' ? 'selected' : ''); ?>>Training</option>
                                    <option value="Meeting" <?php echo e(old('category', $event->category ?? '') === 'Meeting' ? 'selected' : ''); ?>>Meeting</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="event_name">Event Name</label>
                                <input id="event_name" type="text" name="event_name" class="form-control"
                                    value="<?php echo e(old('event_name', $event->event_name ?? '')); ?>" maxlength="255" required
                                    <?php echo e($trainingEvent ? 'readonly' : ''); ?>>
                            </div>
                            <div class="form-group">
                                <label for="event_date">Date</label>
                                <input id="event_date" type="date" name="date" class="form-control"
                                    value="<?php echo e(old('date', isset($event) ? \Carbon\Carbon::parse($event->date)->format('Y-m-d') : '')); ?>" required
                                    <?php echo e($trainingEvent ? 'readonly' : ''); ?>>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="start_plan">Start</label>
                                        <input id="start_plan" type="time" name="start_plan" class="form-control"
                                            value="<?php echo e(old('start_plan', isset($event) ? substr($event->start_plan, 0, 5) : '')); ?>" required
                                            <?php echo e($trainingEvent ? 'readonly' : ''); ?>>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="finish_plan">Finish</label>
                                        <input id="finish_plan" type="time" name="finish_plan" class="form-control"
                                            value="<?php echo e(old('finish_plan', isset($event) ? substr($event->finish_plan, 0, 5) : '')); ?>" required
                                            <?php echo e($trainingEvent ? 'readonly' : ''); ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> <?php echo e($event ? 'Simpan Perubahan' : 'Buat Event'); ?>

                            </button>
                        </div>
                    </form>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Daftar Event</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Event</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e(\Carbon\Carbon::parse($item->date)->format('d-m-Y')); ?></td>
                                        <td><?php echo e($item->event_name); ?><br><small><?php echo e($item->category); ?></small></td>
                                        <td><a class="btn btn-primary btn-xs" href="/Event/<?php echo e($item->id); ?>">Buka</a></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="3" class="text-center">Belum ada event.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php echo e($events->links()); ?>

                    </div>
                </div>
            </div>

            <?php if($event): ?>
                <div class="col-md-8">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Peserta: <?php echo e($event->event_name); ?></h3>
                            <div class="box-tools pull-right">
                                <form method="POST" action="/Event/<?php echo e($event->id); ?>/Delete"
                                    onsubmit="return confirm('Hapus event ini beserta seluruh pesertanya?')">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-danger btn-xs">
                                        <i class="fa fa-trash"></i> Hapus Event
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="box-body">
                            <?php if($event->id_training_schedule): ?>
                                <p>Peserta dari training invitation telah dimasukkan otomatis. Peserta tambahan tetap dapat ditambahkan secara manual.</p>
                            <?php else: ?>
                                <p>Event manual: tambahkan data peserta melalui form berikut.</p>
                            <?php endif; ?>
                            <form method="POST" action="/Event/<?php echo e($event->id); ?>/attendance" class="well">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>ID Peserta</label>
                                        <input type="number" name="id_entrant" value="0" class="form-control" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Nama Peserta</label>
                                        <input type="text" name="entrant_name" class="form-control" maxlength="255" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Institusi</label>
                                        <input type="text" name="institution" class="form-control" maxlength="255" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-success form-control">
                                            <i class="fa fa-plus"></i> Tambah Peserta
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="status" value="0">
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID Peserta</th>
                                            <th>Nama Peserta</th>
                                            <th>Institusi</th>
                                            <th>Check-in</th>
                                            <th>Check-out</th>
                                            <th>Status</th>
                                            <th>Kode</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($attendance->id_entrant); ?></td>
                                                <td><?php echo e($attendance->entrant_name); ?></td>
                                                <td><?php echo e($attendance->institution); ?></td>
                                                <td><?php echo e($attendance->checkin); ?></td>
                                                <td><?php echo e($attendance->checkout); ?></td>
                                                <td><?php echo e($attendance->status); ?></td>
                                                <td><?php echo e($attendance->entrant_code); ?></td>
                                                <td class="text-nowrap">
                                                    <a href="/Event/QRCode/<?php echo e($attendance->id); ?>"
                                                        class="btn btn-info btn-xs" title="Tampilkan QR Code PDF" target="_blank">
                                                        <i class="fa fa-qrcode"></i>
                                                    </a>
                                                    <form method="POST" action="/Event/<?php echo e($event->id); ?>/attendance/<?php echo e($attendance->id); ?>/Delete"
                                                        style="display:inline;" onsubmit="return confirm('Hapus peserta ini?')">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr><td colspan="8" class="text-center">Belum ada peserta.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php if($event): ?>
    <div class="modal fade" id="attendance-modal" tabindex="-1" role="dialog" aria-labelledby="attendance-modal-title">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="attendance-form" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="attendance-modal-title">Edit Peserta</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>ID Peserta</label>
                            <input id="edit-entrant" type="number" name="id_entrant" value="0" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Peserta</label>
                            <input id="edit-name" type="text" name="entrant_name" class="form-control" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label>Institusi</label>
                            <input id="edit-institution" type="text" name="institution" class="form-control" maxlength="255" required>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Check-in</label>
                                <input id="edit-checkin" type="datetime-local" name="checkin" class="form-control">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Check-out</label>
                                <input id="edit-checkout" type="datetime-local" name="checkout" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <input id="edit-status" type="number" name="status" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    (function () {
        var eventId = <?php echo json_encode($event->id ?? null, 15, 512) ?>;
        var scheduleSelect = document.getElementById('schedule');
        if (scheduleSelect && !scheduleSelect.disabled) {
            var eventFields = [
                document.getElementById('event_name'),
                document.getElementById('event_date'),
                document.getElementById('start_plan'),
                document.getElementById('finish_plan')
            ];
            function setManualMode(manual) {
                for (var fieldIndex = 0; fieldIndex < eventFields.length; fieldIndex++) {
                    eventFields[fieldIndex].readOnly = !manual;
                }
                document.getElementById('category').disabled = !manual;
            }

            scheduleSelect.addEventListener('change', function () {
                var selected = this.options[this.selectedIndex];
                if (!this.value) {
                    document.getElementById('event_name').value = '';
                    document.getElementById('event_date').value = '';
                    document.getElementById('start_plan').value = '';
                    document.getElementById('finish_plan').value = '';
                    setManualMode(true);
                    return;
                }

                setManualMode(false);
                document.getElementById('category').value = 'Training';
                document.getElementById('event_name').value = selected.dataset.name || '';
                document.getElementById('event_date').value = (selected.dataset.date || '').substring(0, 10);
                document.getElementById('start_plan').value = (selected.dataset.start || '').substring(0, 5);
                document.getElementById('finish_plan').value = (selected.dataset.finish || '').substring(0, 5);
            });
            if (scheduleSelect.value) {
                scheduleSelect.dispatchEvent(new Event('change'));
            }
        }

        var editButtons = document.querySelectorAll('.edit-attendance');
        for (var i = 0; i < editButtons.length; i++) {
            editButtons[i].addEventListener('click', function () {
                var button = this;
                var form = document.getElementById('attendance-form');
                form.action = '/Event/' + eventId + '/attendance/' + button.dataset.id;
                document.getElementById('edit-entrant').value = button.dataset.entrant || '0';
                document.getElementById('edit-name').value = button.dataset.name;
                document.getElementById('edit-institution').value = button.dataset.institution;
                document.getElementById('edit-checkin').value = button.dataset.checkin;
                document.getElementById('edit-checkout').value = button.dataset.checkout;
                document.getElementById('edit-status').value = button.dataset.status;
                if (window.jQuery_3_2_1) {
                    window.jQuery_3_2_1('#attendance-modal').modal('show');
                }
            });
        }
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page\event\index.blade.php ENDPATH**/ ?>