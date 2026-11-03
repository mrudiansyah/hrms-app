

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Schedule</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode</h3>
            </div>
            <div class="box-body">
                <form action="<?php echo e(url('/qcc_schedule')); ?>" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="periode_id">Periode QCC</label>
                        <select class="form-control" id="periode_id" name="periode_id" required>
                            <option value="">Pilih tema QCC</option>
                            <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($period->id); ?>"
                                    <?php echo e(optional($selectedPeriod)->id == $period->id ? 'selected' : ''); ?>>
                                    <?php echo e($period->sugested_thema); ?> (<?php echo e(\Illuminate\Support\Carbon::parse($period->periode_start)->format('d M Y')); ?>

                                    – <?php echo e(\Illuminate\Support\Carbon::parse($period->periode_finish)->format('d M Y')); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
                </form>
            </div>
        </div>

        <?php if($selectedPeriod): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo e($selectedPeriod->sugested_thema); ?></h3>
                    <p class="text-muted">
                        <?php echo e(\Illuminate\Support\Carbon::parse($selectedPeriod->periode_start)->format('d M Y')); ?>

                        – <?php echo e(\Illuminate\Support\Carbon::parse($selectedPeriod->periode_finish)->format('d M Y')); ?>

                        
                    </p>
                </div>
                <div class="box-body">
                    <?php if($schedules->isEmpty()): ?>
                        <div class="alert alert-warning">
                            Belum ada schedule untuk periode ini. Pastikan Master Steps 1–8 sudah terisi, lalu buat ulang periode.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive qcc-timeline">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="qcc-fixed">ID Step</th>
                                        <th rowspan="2" class="qcc-fixed qcc-description">Step Description</th>
                                        <th rowspan="2" class="qcc-fixed qcc-date">Start</th>
                                        <th rowspan="2" class="qcc-fixed qcc-date">Finish</th>
                                        <th rowspan="2" class="qcc-action">Aksi</th>
                                        <?php $__currentLoopData = $monthGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <th colspan="<?php echo e($month['count']); ?>" class="text-center">
                                                <?php echo e($month['label']); ?>

                                            </th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                    <tr>
                                        <?php $__currentLoopData = $weeks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <th class="text-center qcc-week" title="<?php echo e($week['start']); ?> – <?php echo e($week['finish']); ?>">
                                                W<?php echo e($week['number']); ?>

                                            </th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php ($formId = 'update-schedule-' . $schedule->id); ?>
                                        <?php ($planStart = \Illuminate\Support\Carbon::parse($schedule->plan_start)->toDateString()); ?>
                                        <?php ($planFinish = \Illuminate\Support\Carbon::parse($schedule->plan_finish)->toDateString()); ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($schedule->step_sequance); ?></td>
                                            <td class="qcc-description"><?php echo e($schedule->step_description); ?></td>
                                            <td>
                                                <?php if($isCommittee): ?>
                                                    <input class="form-control input-sm" type="date" name="plan_start"
                                                        form="<?php echo e($formId); ?>" value="<?php echo e($planStart); ?>" required>
                                                <?php else: ?>
                                                    <?php echo e(\Illuminate\Support\Carbon::parse($planStart)->format('d/m/Y')); ?>

                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($isCommittee): ?>
                                                    <input class="form-control input-sm" type="date" name="plan_finish"
                                                        form="<?php echo e($formId); ?>" value="<?php echo e($planFinish); ?>" required>
                                                <?php else: ?>
                                                    <?php echo e(\Illuminate\Support\Carbon::parse($planFinish)->format('d/m/Y')); ?>

                                                <?php endif; ?>
                                            </td>
                                            <td class="qcc-action">
                                                <?php if($isCommittee): ?>
                                                    <form id="<?php echo e($formId); ?>" action="<?php echo e(url('/qcc_schedule/' . $schedule->id)); ?>"
                                                        method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PUT'); ?>
                                                        <button class="btn btn-primary btn-xs" type="submit" title="Simpan tanggal">
                                                            <i class="fa fa-save"></i>
                                                        </button>
                                                        <input type="hidden" name="periode_id" value="<?php echo e($selectedPeriod->id); ?>">
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php $__currentLoopData = $weeks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php ($active = $planStart <= $week['finish'] && $planFinish >= $week['start']); ?>
                                                <?php ($previousActive = !$loop->first && $planStart <= $weeks[$loop->index - 1]['finish'] && $planFinish >= $weeks[$loop->index - 1]['start']); ?>
                                                <?php ($nextActive = !$loop->last && $planStart <= $weeks[$loop->index + 1]['finish'] && $planFinish >= $weeks[$loop->index + 1]['start']); ?>
                                                <td class="qcc-week text-center"
                                                    title="<?php echo e($active ? $schedule->step_description : ''); ?>">
                                                    <?php if($active): ?>
                                                        <span class="qcc-week-bar <?php echo e($previousActive ? '' : 'qcc-week-bar-start'); ?> <?php echo e($nextActive ? '' : 'qcc-week-bar-end'); ?>"
                                                            role="img" aria-label="Schedule aktif"></span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>
<style>
    .qcc-navigation .btn { margin-right: 6px; }
    .qcc-timeline {
        overflow-x: auto;
        border: 1px solid #b8c9dc;
        border-radius: 4px;
    }
    .qcc-timeline table {
        min-width: max-content;
        margin-bottom: 0;
        background: #fff;
    }
    .qcc-timeline th, .qcc-timeline td { vertical-align: middle !important; }
    .qcc-timeline .table-bordered { border-color: #d4dfeb; }
    .qcc-timeline .table-bordered > thead > tr > th {
        color: #24415f !important;
        border-color: #c5d6e8 !important;
        background-color: #e8f1fa !important;
        background-image: linear-gradient(180deg, #f3f8fd 0%, #e2edf8 100%) !important;
        text-shadow: none;
    }
    .qcc-fixed { min-width: 90px; }
    .qcc-description { min-width: 220px; }
    .qcc-date { min-width: 140px; }
    .qcc-week { min-width: 48px; }
    .qcc-timeline .table-bordered > thead > tr > th.qcc-month-label {
        color: #214f5b !important;
        border-color: #c1dcdf !important;
        background-color: #e3f1f2 !important;
        background-image: linear-gradient(180deg, #f0f8f8 0%, #dceeee 100%) !important;
    }
    .qcc-timeline .table-bordered > tbody > tr > td { border-color: #d7e2ee; }
    .qcc-timeline .table-bordered > tbody > tr:hover > td { background-color: #f3f8fc; }
    .qcc-week-bar { position: relative; z-index: 1; display: block; width: calc(100% + 16px); height: 8px; margin-left: -8px; background-color: #4b83bd; }
    .qcc-week-bar-start { border-top-left-radius: 4px; border-bottom-left-radius: 4px; }
    .qcc-week-bar-end { border-top-right-radius: 4px; border-bottom-right-radius: 4px; }
    .qcc-action { min-width: 58px; text-align: center; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/schedule.blade.php ENDPATH**/ ?>