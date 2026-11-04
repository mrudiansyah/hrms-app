

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Penilaian Assessment</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode, Kategori, Team, dan Assessor</h3>
            </div>
            <div class="box-body">
                <form action="<?php echo e(url('/qcc_assesment_team')); ?>" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="score-period">Periode</label>
                                <select class="form-control" id="score-period" name="periode_id" required>
                                    <option value="">Pilih periode</option>
                                    <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($period->id); ?>" <?php if((int) $selectedPeriodId === (int) $period->id): echo 'selected'; endif; ?>>
                                            <?php echo e($period->sugested_thema); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="score-category">Kategori</label>
                                <select class="form-control" id="score-category" name="category_id" required>
                                    <option value="">Pilih kategori</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>" <?php if((int) $selectedCategoryId === (int) $category->id): echo 'selected'; endif; ?>>
                                            <?php echo e($category->asses_category); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="score-team">Team QCC</label>
                                <select class="form-control" id="score-team" name="team_period_id" required>
                                    <option value="">Pilih team</option>
                                    <?php $__currentLoopData = $teamPeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teamPeriod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($teamPeriod->id); ?>" <?php if((int) $selectedTeamPeriodId === (int) $teamPeriod->id): echo 'selected'; endif; ?>>
                                            <?php echo e($teamPeriod->group_name); ?><?php echo e($teamPeriod->department ? ' — ' . $teamPeriod->department : ''); ?><?php echo e($teamPeriod->theme_team ? ' — ' . $teamPeriod->theme_team : ''); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="score-assessor">Assessor</label>
                                <select class="form-control" id="score-assessor" name="assesor_id" required>
                                    <option value="">Pilih assessor</option>
                                    <?php $__currentLoopData = $assessors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($assessor->id); ?>" <?php if((int) $selectedAssessorId === (int) $assessor->id): echo 'selected'; endif; ?>>
                                            <?php echo e($assessor->assesor_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan</button>
                    <a class="btn btn-default"
                        href="<?php echo e(url('/qcc_assesor?periode_id=' . $selectedPeriodId . '&category_id=' . $selectedCategoryId)); ?>">
                        <i class="fa fa-users"></i> Kelola Assessor
                    </a>
                </form>
            </div>
        </div>

        <?php if($selectedTeamPeriod && $selectedAssessor): ?>
            <?php ($hasScorableDetails = $details->contains(fn ($detail) => $criteriaByDetail->get($detail->id, collect())->isNotEmpty())); ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                        <form id="score-form" action="<?php echo e(url('/qcc_assesment_team')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id_periode_team" value="<?php echo e($selectedTeamPeriodId); ?>">
                            <input type="hidden" name="id_assesor" value="<?php echo e($selectedAssessorId); ?>">
                            <button class="btn btn-primary" type="submit" <?php if(!$hasScorableDetails): echo 'disabled'; endif; ?>>
                                <i class="fa fa-save"></i> Simpan Penilaian
                            </button>
                        </form>
                    </div>
                    <h3 class="box-title">
                        <?php echo e($selectedTeamPeriod->group_name); ?>

                        <?php if($selectedTeamPeriod->department): ?>
                            <small>— <?php echo e($selectedTeamPeriod->department); ?></small>
                        <?php endif; ?>
                        <?php if($selectedTeamPeriod->theme_team): ?>
                            <small>— <?php echo e($selectedTeamPeriod->theme_team); ?></small>
                        <?php endif; ?>
                    </h3>
                    <p class="text-muted" style="margin-top:8px;">
                        <?php echo e($selectedPeriod->sugested_thema); ?> · <?php echo e($selectedCategory->asses_category); ?>

                        · Assessor: <?php echo e($selectedAssessor->assesor_name); ?>

                    </p>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-gray">
                                <th style="width:25%;">Detail</th>
                                <th>Criteria (point menjadi nilai)</th>
                                <th style="width:25%;">Remark (opsional)</th>
                                <th style="width:50px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $details->groupBy('id_assesment'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessmentId => $assessmentDetails): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php ($assessment = $assessmentDetails->first()); ?>
                                <?php ($subtotal = $assessmentDetails->sum(fn ($detail) => (float) ($scoresByDetail->get($detail->id)->point ?? 0))); ?>
                                <tr class="active">
                                    <th colspan="3">
                                        <div style="display:flex;justify-content:space-between;align-items:center;">
                                            <span><?php echo e($assessment->index_assesment ? $assessment->index_assesment . '. ' : ''); ?><?php echo e($assessment->asses_description); ?></span>
                                            <span>Subtotal:</span>
                                        </div>
                                    </th>
                                    <th colspan="1">
                                        <div style="text-align:center;">
                                            <span data-assessment-subtotal="<?php echo e($assessmentId); ?>"><?php echo e($subtotal); ?></span>
                                        </div>
                                    </th>
                                </tr>
                                <?php $__currentLoopData = $assessmentDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php ($criteria = $criteriaByDetail->get($detail->id, collect())); ?>
                                    <?php ($score = $scoresByDetail->get($detail->id)); ?>
                                    <tr>
                                        <td>
                                            <?php echo e($detail->asses_description_detail); ?>

                                            <?php if($detail->bobot !== null): ?>
                                                <br><small class="text-muted">Bobot detail: <?php echo e($detail->bobot); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($criteria->isNotEmpty()): ?>
                                                <select class="form-control js-score-criteria"
                                                    name="scores[<?php echo e($detail->id); ?>][id_assesment_criteria]"
                                                    data-assessment-id="<?php echo e($assessmentId); ?>"
                                                    form="score-form" required>
                                                    <option value="">Pilih satu criteria</option>
                                                    <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($item->id); ?>" data-point="<?php echo e($item->point); ?>"
                                                            <?php if((string) old('scores.' . $detail->id . '.id_assesment_criteria', $score->id_assesment_criteria ?? '') === (string) $item->id): echo 'selected'; endif; ?>>
                                                            <?php echo e($item->point); ?> poin — <?php echo e($item->criteria_description); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <small class="text-muted">Nilai mengikuti point criteria yang dipilih.</small>
                                            <?php else: ?>
                                                <span class="text-warning">Belum ada criteria untuk detail ini.</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <textarea class="form-control"
                                                name="scores[<?php echo e($detail->id); ?>][remark]" form="score-form" rows="2"
                                                placeholder="Catatan assessor (opsional)"><?php echo e(old('scores.' . $detail->id . '.remark', $score->remark ?? '')); ?></textarea>
                                        </td>
                                        <td style="text-align:center;">
                                            <?php if($score): ?>
                                                <form action="<?php echo e(url('/qcc_assesment_team/' . $score->id)); ?>" method="POST"
                                                    onsubmit="return confirm('Hapus nilai untuk detail ini?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button class="btn btn-danger btn-xs" type="submit">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4" class="text-center text-muted">Belum ada assessment detail untuk kategori ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if($details->isNotEmpty()): ?>
                            <?php ($totalPoint = $details->sum(fn ($detail) => (float) ($scoresByDetail->get($detail->id)->point ?? 0))); ?>
                            <tfoot>
                                <tr class="bg-gray">
                                    <th colspan="3" class="text-right">Total Point</th>
                                    <th class="text-center"><span id="score-total"><?php echo e($totalPoint); ?></span></th>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        <?php elseif($selectedPeriodId && $selectedCategoryId): ?>
            <div class="callout callout-info">
                <?php if($teamPeriods->isEmpty()): ?>
                    <p>Belum ada team yang terdaftar pada periode ini.</p>
                <?php elseif($assessors->isEmpty()): ?>
                    <p>Belum ada assessor untuk periode dan kategori ini. Tambahkan assessor terlebih dahulu.</p>
                <?php else: ?>
                    <p>Pilih team dan assessor untuk mulai mengisi penilaian.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="callout callout-info">
                <p>Pilih periode dan kategori assessment untuk memulai penilaian.</p>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
<script>
    $(function () {
        function updateScoreTotals() {
            var totalsByAssessment = {};
            var total = 0;

            $('.js-score-criteria').each(function () {
                var point = parseFloat($(this).find('option:selected').data('point')) || 0;
                var assessmentId = $(this).data('assessment-id');
                totalsByAssessment[assessmentId] = (totalsByAssessment[assessmentId] || 0) + point;
                total += point;
            });

            $('[data-assessment-subtotal]').each(function () {
                var subtotal = totalsByAssessment[$(this).data('assessment-subtotal')] || 0;
                $(this).text(Number(subtotal.toFixed(2)));
            });
            $('#score-total').text(Number(total.toFixed(2)));
        }

        $('.js-score-criteria').on('change', updateScoreTotals);
        updateScoreTotals();

        $('#score-form').on('submit', function () {
            $(this).find('button[type="submit"]')
                .prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        });

        $('#score-period, #score-category').on('change', function () {
            var form = $('#score-period').closest('form');
            if (this.id === 'score-period') {
                $('#score-team').val('');
            }
            $('#score-assessor').val('');
            form[0].submit();
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/assessment_scoring.blade.php ENDPATH**/ ?>