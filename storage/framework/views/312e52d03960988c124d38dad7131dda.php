

<?php $__env->startSection('Contents'); ?>
<style>
    .qcc-summary-table thead th {
        color: #fff;
        border-color: #315a7d !important;
        vertical-align: middle;
    }

    .qcc-summary-table thead tr:first-child th {
        background-color: #4b708d !important;
        background-image: none !important;
    }

    .qcc-summary-table thead tr:nth-child(2) th {
        background-color: #4b708d !important;
        background-image: none !important;
        color: #fff;
        border-color: #3d617c !important;
    }

    .qcc-summary-table thead th[rowspan="2"]:last-child {
        background-color: #4b708d !important;
        background-image: none !important;
        border-color: #3d617c !important;
    }

    .qcc-summary-table tbody tr:hover {
        background-color: #f0f8fc;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1>QCC <small>Summary Penilaian</small></h1>
    </section>
    <section class="content">
        <?php echo $__env->make('qcc._navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div style="height:15px;"></div>
        <?php echo $__env->make('qcc._messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pilih Periode dan Kategori Assessment</h3>
            </div>
            <div class="box-body">
                <form action="<?php echo e(url('/qcc_assesment_summary')); ?>" method="GET">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="summary-period">Periode</label>
                                <select class="form-control" id="summary-period" name="periode_id" required>
                                    <option value="">Pilih periode</option>
                                    <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($period->id); ?>" <?php if((int) $selectedPeriodId === (int) $period->id): echo 'selected'; endif; ?>>
                                            <?php echo e($period->sugested_thema); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="summary-category">Kategori</label>
                                <select class="form-control" id="summary-category" name="category_id" required>
                                    <option value="">Pilih kategori</option>
                                    <option value="all" <?php if($allCategories): echo 'selected'; endif; ?>>Semua Kategori</option>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>" <?php if((int) $selectedCategoryId === (int) $category->id): echo 'selected'; endif; ?>>
                                            <?php echo e($category->asses_category); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tampilkan Summary</button>
                </form>
            </div>
        </div>

        <?php if($selectedPeriod && ($selectedCategory || $allCategories)): ?>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <?php echo e($selectedPeriod->sugested_thema); ?> — <?php echo e($allCategories ? 'Semua Kategori' : $selectedCategory->asses_category); ?>

                    </h3>
                    <p class="text-muted" style="margin-top:8px;margin-bottom:0;">
                        Nilai per Assessment dan total merupakan rata-rata nilai assessor yang sudah menilai.
                    </p>
                </div>
                <div class="box-body table-responsive">
                    <?php if($assessments->isEmpty()): ?>
                        <p class="text-center text-muted">Belum ada Assessment pada kategori ini.</p>
                    <?php elseif($summaryRows->isEmpty()): ?>
                        <p class="text-center text-muted">Belum ada team yang terdaftar pada periode ini.</p>
                    <?php else: ?>
                        <table class="table table-bordered table-hover qcc-summary-table">
                            <thead>
                                <tr class="bg-gray">
                                    <th rowspan="2" style="width:60px;vertical-align:middle;">No</th>
                                    <th rowspan="2" style="min-width:180px;vertical-align:middle;">Nama Team</th>
                                    <th rowspan="2" style="min-width:150px;vertical-align:middle;">Department</th>
                                    <?php $__currentLoopData = $assessments->groupBy('id_asses_category'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryAssessments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th colspan="<?php echo e($categoryAssessments->count()); ?>" class="text-center">
                                            <?php echo e($categoryAssessments->first()->asses_category); ?>

                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th rowspan="2" style="min-width:120px;vertical-align:middle;text-align:center;">Total Point</th>
                                </tr>
                                <tr class="bg-gray">
                                    <?php $__currentLoopData = $assessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th style="min-width:130px;text-align:center;"><?php echo e($assessment->index_assesment); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $summaryRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><?php echo e($row->team->group_name); ?></td>
                                        <td><?php echo e($row->team->department ?: '-'); ?></td>
                                        <?php $__currentLoopData = $assessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td class="text-center">
                                                <?php if($row->assessmentPoints[$assessment->id] !== null): ?>
                                                    <?php echo e(number_format($row->assessmentPoints[$assessment->id], 2)); ?>

                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center">
                                            <?php echo e($row->totalPoint === null ? '-' : number_format($row->totalPoint, 2)); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/qcc/assessment_summary.blade.php ENDPATH**/ ?>