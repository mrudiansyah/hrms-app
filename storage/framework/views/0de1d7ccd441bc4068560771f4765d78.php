

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Pengaturan AI Chat <small>Hanya role root</small></h1>
    </section>
    <section class="content">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <div class="alert alert-warning">
            Data hanya dapat dibaca oleh AI. Pilih kolom yang boleh digunakan dan tentukan cakupan baris.
            Cakupan semua baris memberi akses ke semua data dalam tabel tersebut.
        </div>

        <form method="GET" action="<?php echo e(url('/AIChat/Settings')); ?>" style="margin-bottom: 20px">
            <div class="form-group" style="width: 100%; max-width: 600px">
                <label for="tableSelector">Pilih tabel</label>
                <select id="tableSelector" name="table" class="form-control select2" style="width: 100%" onchange="this.form.submit()">
                    <option value="">-- Pilih tabel --</option>
                    <?php $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($table['name']); ?>" <?php if($selectedTable === $table['name']): echo 'selected'; endif; ?>><?php echo e($table['label']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </form>

        <?php if($selectedTable && count($columns)): ?>
            <?php ($currentColumns = $current ? json_decode($current->allowed_columns, true) : []); ?>
            <form method="POST" action="<?php echo e(url('/AIChat/Settings')); ?>" class="box box-primary">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="table_name" value="<?php echo e($selectedTable); ?>">
                <div class="box-header with-border"><h3 class="box-title"><?php echo e($selectedTable); ?></h3></div>
                <div class="box-body">
                    <div class="form-group">
                        <label>Cakupan baris</label>
                        <select name="row_scope" id="rowScope" class="form-control" required>
                            <option value="employee" <?php if(($current->row_scope ?? '') === 'employee'): echo 'selected'; endif; ?>>Data milik karyawan yang login</option>
                            <option value="department" <?php if(($current->row_scope ?? '') === 'department'): echo 'selected'; endif; ?>>Data departemen karyawan yang login</option>
                            <option value="all" <?php if(($current->row_scope ?? '') === 'all'): echo 'selected'; endif; ?>>Semua baris</option>
                        </select>
                    </div>
                    <div class="form-group" id="scopeColumnGroup">
                        <label>Kolom pembatas data</label>
                        <select name="scope_column" class="form-control">
                            <option value="">-- Pilih kolom --</option>
                            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($column['name']); ?>" <?php if(($current->scope_column ?? '') === $column['name']): echo 'selected'; endif; ?>><?php echo e($column['name']); ?> (<?php echo e($column['type']); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="help-block">Kolom ini dibandingkan dengan ID karyawan atau ID departemen dari akun yang login.</p>
                    </div>
                    <div class="form-group" style="width: 100%; margin-top: 15px">
                        <label>Kolom yang boleh digunakan AI</label>
                        <div style="margin: 8px 0">
                            <button type="button" class="btn btn-default btn-sm" id="selectAllColumns">Pilih semua</button>
                            <button type="button" class="btn btn-default btn-sm" id="clearAllColumns">Lepas semua</button>
                        </div>
                        <div class="row">
                            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-sm-4">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" class="ai-allowed-column" name="allowed_columns[]" value="<?php echo e($column['name']); ?>" <?php if(in_array($column['name'], $currentColumns ?? [], true)): echo 'checked'; endif; ?>>
                                        <?php echo e($column['name']); ?> <small>(<?php echo e($column['type']); ?>)</small>
                                    </label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" type="submit">Simpan izin</button>
                </div>
            </form>
        <?php elseif($selectedTable): ?>
            <div class="alert alert-danger">Kolom tabel tidak dapat dibaca.</div>
        <?php endif; ?>

        <?php if($current): ?>
            <form method="POST" action="<?php echo e(url('/AIChat/Settings/Delete')); ?>" onsubmit="return confirm('Hapus izin AI untuk tabel ini?')">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="table_name" value="<?php echo e($current->table_name); ?>">
                <button class="btn btn-danger" type="submit">Hapus izin tabel</button>
            </form>
        <?php endif; ?>
    </section>
</div>
<script>
    (function () {
        if (window.jQuery && jQuery.fn.select2) {
            jQuery('#tableSelector').select2({
                width: '100%',
                placeholder: '-- Cari tabel --',
                allowClear: true
            });
        }

        const scope = document.getElementById('rowScope');
        const group = document.getElementById('scopeColumnGroup');
        if (scope && group) {
            const updateScopeColumn = function () {
                group.style.display = scope.value === 'all' ? 'none' : '';
            };
            scope.addEventListener('change', updateScopeColumn);
            updateScopeColumn();
        }

        const columns = document.querySelectorAll('.ai-allowed-column');
        const setColumnSelection = function (checked) {
            columns.forEach(function (column) {
                column.checked = checked;
            });
        };
        const selectAllButton = document.getElementById('selectAllColumns');
        const clearAllButton = document.getElementById('clearAllColumns');
        if (selectAllButton) {
            selectAllButton.addEventListener('click', function () {
                setColumnSelection(true);
            });
        }
        if (clearAllButton) {
            clearAllButton.addEventListener('click', function () {
                setColumnSelection(false);
            });
        }
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/ai_chat/settings.blade.php ENDPATH**/ ?>