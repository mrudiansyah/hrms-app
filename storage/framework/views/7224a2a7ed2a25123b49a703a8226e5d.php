

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Activity</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode QCC</h3>
            </div>
            <div class="box-body">
                <form action="<?php echo e(url('/qcc_activity')); ?>" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="activity_periode_id">Periode</label>
                        <select class="form-control" id="activity_periode_id" name="periode_id" required>
                            <option value="">Pilih periode QCC</option>
                            <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($period->id); ?>" <?php if($selectedPeriod && (int) $selectedPeriod->id === (int) $period->id): echo 'selected'; endif; ?>>
                                    <?php echo e($period->sugested_thema); ?>

                                    (<?php echo e(\Illuminate\Support\Carbon::parse($period->periode_start)->format('d/m/Y')); ?> - <?php echo e(\Illuminate\Support\Carbon::parse($period->periode_finish)->format('d/m/Y')); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
                </form>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-tasks text-blue"></i> Team QCC
                    <?php if($selectedPeriod): ?>
                        <small>
                            <?php echo e(\Illuminate\Support\Carbon::parse($selectedPeriod->periode_start)->format('d M Y')); ?>

                            – <?php echo e(\Illuminate\Support\Carbon::parse($selectedPeriod->periode_finish)->format('d M Y')); ?>

                        </small>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-gray">
                            <th>Team</th>
                            <th>Theme of Periode</th>
                            <th>Theme of Team</th>
                            <th style="width:90px;" class="text-center">Anggota</th>
                            <th style="width:180px;">Progress</th>
                            <th style="width:120px;" class="text-center">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $teamPeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teamPeriod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($teamPeriod->group_name); ?></strong></td>
                                <td><?php echo e($teamPeriod->sugested_thema); ?></td>
                                <td>
                                    <?php echo e($teamPeriod->theme_team ?: '-'); ?>

                                    <div class="pull-right">
                                        <button class="btn btn-warning btn-xs" type="button" title="Edit tema team"
                                            data-toggle="modal" data-target="#edit-team-theme-<?php echo e($teamPeriod->id); ?>">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center"><?php echo e($teamPeriod->member_count); ?></td>
                                <td>
                                    <?php ($progressPercent = (int) round(($teamPeriod->progress_step / 8) * 100)); ?>
                                    <div>Step <?php echo e($teamPeriod->progress_step); ?> / 8</div>
                                    <div class="progress" style="height:10px; margin:5px 0 0;">
                                        <div class="progress-bar progress-bar-success" role="progressbar"
                                            aria-valuenow="<?php echo e($progressPercent); ?>" aria-valuemin="0" aria-valuemax="100"
                                            style="width:<?php echo e($progressPercent); ?>%;"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <form action="<?php echo e(url('/qcc_activity/'.$teamPeriod->id.'/open')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-primary btn-sm" type="submit">
                                            <i class="fa fa-folder"></i> Show Detail
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted" style="padding:30px;">
                                    <i class="fa fa-tasks fa-2x"></i>
                                    <p style="margin:10px 0 0;">
                                        <?php echo e(!$selectedPeriod ? 'Belum ada periode QCC.' : ($isCommittee ? 'Belum ada team yang terdaftar pada periode QCC.' : 'Anda belum terdaftar sebagai anggota team QCC pada periode ini.')); ?>

                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
              </div>
              <?php if($isCommittee): ?>
                  <?php $__currentLoopData = $teamPeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teamPeriod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="modal fade" id="edit-team-theme-<?php echo e($teamPeriod->id); ?>" tabindex="-1" role="dialog"
                          aria-labelledby="edit-team-theme-title-<?php echo e($teamPeriod->id); ?>">
                          <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                  <form action="<?php echo e(url('/qcc_activity/'.$teamPeriod->id.'/theme')); ?>" method="POST">
                                      <?php echo csrf_field(); ?>
                                      <?php echo method_field('PUT'); ?>
                                      <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                          </button>
                                          <h4 class="modal-title" id="edit-team-theme-title-<?php echo e($teamPeriod->id); ?>">
                                              Edit Tema Team <?php echo e($teamPeriod->group_name); ?>

                                          </h4>
                                      </div>
                                      <div class="modal-body">
                                          <div class="form-group">
                                              <label for="theme-team-<?php echo e($teamPeriod->id); ?>">Tema Team</label>
                                              <input class="form-control" id="theme-team-<?php echo e($teamPeriod->id); ?>"
                                                  name="theme_team" value="<?php echo e($teamPeriod->theme_team); ?>"
                                                  maxlength="255" required>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                          <button type="submit" class="btn btn-primary">
                                              <i class="fa fa-save"></i> Simpan
                                          </button>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/activities.blade.php ENDPATH**/ ?>