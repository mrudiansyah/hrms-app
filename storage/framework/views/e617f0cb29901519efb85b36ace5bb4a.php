

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Activity</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <a class="btn btn-default btn-sm pull-right" href="<?php echo e(url('/qcc_activity')); ?>">
                    <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <h3 class="box-title"><?php echo e($teamPeriod->group_name); ?> <small>— <?php echo e($period->sugested_thema); ?></small></h3>
                <p class="text-muted" style="margin:8px 0 0;">
                    <?php echo e(\Illuminate\Support\Carbon::parse($period->periode_start)->format('d M Y')); ?>

                    – <?php echo e(\Illuminate\Support\Carbon::parse($period->periode_finish)->format('d M Y')); ?>

                    · Setiap step memiliki baris Plan dan Actual.
                </p>
            </div>
            <div class="box-body">
                <?php if($schedules->isEmpty()): ?>
                    <div class="alert alert-warning">Belum ada schedule pada periode ini.</div>
                <?php else: ?>
                    <div class="table-responsive qcc-activity-timeline">
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="qcc-step-number">Step</th>
                                    <th rowspan="2" class="qcc-step-description">Deskripsi</th>
                                    <th rowspan="2" class="qcc-row-type">Jenis</th>
                                    <th rowspan="2" class="qcc-date-column">Start</th>
                                    <th rowspan="2" class="qcc-date-column">Finish</th>
                                    <th rowspan="2" class="qcc-action-column">Aksi</th>
                                    <?php $__currentLoopData = $monthGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th colspan="<?php echo e($month['count']); ?>" class="text-center qcc-month-label"><?php echo e($month['label']); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                <tr>
                                    <?php $__currentLoopData = $weeks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="text-center qcc-week" title="<?php echo e($week['start']); ?> – <?php echo e($week['finish']); ?>">W<?php echo e($week['number']); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php ($actual = $actuals->get($schedule->id)); ?>
                                    <?php ($planStart = \Illuminate\Support\Carbon::parse($schedule->plan_start)->toDateString()); ?>
                                    <?php ($planFinish = \Illuminate\Support\Carbon::parse($schedule->plan_finish)->toDateString()); ?>
                                    <?php ($actualStart = $actual && $actual->actual_start ? \Illuminate\Support\Carbon::parse($actual->actual_start)->toDateString() : null); ?>
                                    <?php ($actualFinish = $actual && $actual->actual_finish ? \Illuminate\Support\Carbon::parse($actual->actual_finish)->toDateString() : null); ?>
                                    <?php ($modalId = 'edit-actual-' . ($actual->id ?? $schedule->id)); ?>
                                    <tr class="qcc-plan-row">
                                        <td rowspan="2" class="text-center qcc-step-number-cell"><?php echo e($schedule->step_sequance); ?></td>
                                        <td rowspan="2" class="qcc-step-description-cell"><?php echo e($schedule->step_description); ?></td>
                                        <td><span class="label label-primary">Plan</span></td>
                                        <td><?php echo e(\Illuminate\Support\Carbon::parse($schedule->plan_start)->format('d/m/Y')); ?></td>
                                        <td><?php echo e(\Illuminate\Support\Carbon::parse($schedule->plan_finish)->format('d/m/Y')); ?></td>
                                        <td class="text-center text-muted">-</td>
                                        <?php $__currentLoopData = $weeks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php ($active = $planStart <= $week['finish'] && $planFinish >= $week['start']); ?>
                                            <?php ($previousActive = !$loop->first && $planStart <= $weeks[$loop->index - 1]['finish'] && $planFinish >= $weeks[$loop->index - 1]['start']); ?>
                                            <?php ($nextActive = !$loop->last && $planStart <= $weeks[$loop->index + 1]['finish'] && $planFinish >= $weeks[$loop->index + 1]['start']); ?>
                                            <td class="qcc-week text-center" title="<?php echo e($active ? $schedule->step_description.' (Plan)' : ''); ?>">
                                                <?php if($active): ?>
                                                    <span class="qcc-week-bar qcc-plan-bar <?php echo e($previousActive ? '' : 'qcc-week-bar-start'); ?> <?php echo e($nextActive ? '' : 'qcc-week-bar-end'); ?>" role="img" aria-label="Plan aktif"></span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                    <tr class="qcc-actual-row">
                                        <td><span class="label label-success">Actual</span></td>
                                        <td><?php echo e($actualStart ? \Illuminate\Support\Carbon::parse($actualStart)->format('d/m/Y') : '-'); ?></td>
                                        <td><?php echo e($actualFinish ? \Illuminate\Support\Carbon::parse($actualFinish)->format('d/m/Y') : '-'); ?></td>
                                        <td class="text-center">
                                            <?php if($actual): ?>
                                                <button class="btn btn-success btn-xs" type="button" data-toggle="modal" data-target="#<?php echo e($modalId); ?>">
                                                    <i class="fa fa-pencil"></i> Input Actual
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php $__currentLoopData = $weeks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php ($active = $actualStart && $actualFinish && $actualStart <= $week['finish'] && $actualFinish >= $week['start']); ?>
                                            <?php ($previousActive = $active && !$loop->first && $actualStart <= $weeks[$loop->index - 1]['finish'] && $actualFinish >= $weeks[$loop->index - 1]['start']); ?>
                                            <?php ($nextActive = $active && !$loop->last && $actualStart <= $weeks[$loop->index + 1]['finish'] && $actualFinish >= $weeks[$loop->index + 1]['start']); ?>
                                            <td class="qcc-week text-center" title="<?php echo e($active ? $schedule->step_description.' (Actual)' : ''); ?>">
                                                <?php if($active): ?>
                                                    <span class="qcc-week-bar qcc-actual-bar <?php echo e($previousActive ? '' : 'qcc-week-bar-start'); ?> <?php echo e($nextActive ? '' : 'qcc-week-bar-end'); ?>" role="img" aria-label="Actual aktif"></span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php ($actual = $actuals->get($schedule->id)); ?>
                        <?php if($actual): ?>
                            <?php ($modalId = 'edit-actual-' . $actual->id); ?>
                            <?php ($actualStart = $actual->actual_start ? \Illuminate\Support\Carbon::parse($actual->actual_start)->toDateString() : ''); ?>
                            <?php ($actualFinish = $actual->actual_finish ? \Illuminate\Support\Carbon::parse($actual->actual_finish)->toDateString() : ''); ?>
                            <div class="modal fade" id="<?php echo e($modalId); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo e($modalId); ?>-title">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <form action="<?php echo e(url('/qcc_activity_actual/'.$actual->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="<?php echo e($modalId); ?>-title">Actual — Step <?php echo e($schedule->step_sequance); ?>: <?php echo e($schedule->step_description); ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="actual_start_<?php echo e($actual->id); ?>">Actual Start</label>
                                                            <input class="form-control" type="date" id="actual_start_<?php echo e($actual->id); ?>" name="actual_start" value="<?php echo e($actualStart); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label for="actual_finish_<?php echo e($actual->id); ?>">Actual Finish</label>
                                                            <input class="form-control" type="date" id="actual_finish_<?php echo e($actual->id); ?>" name="actual_finish" value="<?php echo e($actualFinish); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="resume_meeting_<?php echo e($actual->id); ?>">Resume Meeting</label>
                                                    <textarea class="form-control" id="resume_meeting_<?php echo e($actual->id); ?>" name="resume_meeting" rows="4" maxlength="5000"><?php echo e($actual->resume_meeting); ?></textarea>
                                                </div>
                                                <div class="form-group" style="margin-bottom:0;">
                                                    <label for="report_improvement_<?php echo e($actual->id); ?>">Report Improvement</label>
                                                    <textarea class="form-control" id="report_improvement_<?php echo e($actual->id); ?>" name="report_improvement" rows="4" maxlength="5000"><?php echo e($actual->report_improvement); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan Actual</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<style>
    .qcc-activity-timeline { overflow-x: auto; border: 1px solid #b8c9dc; border-radius: 4px; }
    .qcc-activity-timeline table { min-width: max-content; margin-bottom: 0; background: #fff; }
    .qcc-activity-timeline th, .qcc-activity-timeline td { vertical-align: middle !important; }
    .qcc-activity-timeline .table-bordered { border-color: #d4dfeb; }
    .qcc-activity-timeline .table-bordered > thead > tr > th {
        color: #24415f !important;
        border-color: #c5d6e8 !important;
        background-color: #e8f1fa !important;
        background-image: linear-gradient(180deg, #f3f8fd 0%, #e2edf8 100%) !important;
    }
    .qcc-activity-timeline .table-bordered > thead > tr > th.qcc-month-label {
        color: #214f5b !important;
        border-color: #c1dcdf !important;
        background-color: #e3f1f2 !important;
        background-image: linear-gradient(180deg, #f0f8f8 0%, #dceeee 100%) !important;
    }
    .qcc-step-number { min-width: 55px; }
    .qcc-step-description { min-width: 220px; }
    .qcc-row-type { min-width: 75px; }
    .qcc-date-column { min-width: 100px; }
    .qcc-action-column { min-width: 115px; }
    .qcc-step-number-cell { font-weight: bold; }
    .qcc-step-description-cell { min-width: 220px; }
    .qcc-week { min-width: 42px; }
    .qcc-plan-row > td { border-top-color: #c7d5e4 !important; }
    .qcc-actual-row > td { border-bottom-color: #c7d5e4 !important; }
    .qcc-week-bar { position: relative; z-index: 1; display: block; width: calc(100% + 14px); height: 7px; margin-left: -7px; }
    .qcc-plan-bar { background-color: #4b83bd; }
    .qcc-actual-bar { background-color: #49a56a; }
    .qcc-week-bar-start { border-top-left-radius: 4px; border-bottom-left-radius: 4px; }
    .qcc-week-bar-end { border-top-right-radius: 4px; border-bottom-right-radius: 4px; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/activity.blade.php ENDPATH**/ ?>