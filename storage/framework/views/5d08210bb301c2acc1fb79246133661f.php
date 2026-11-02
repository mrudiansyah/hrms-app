<?php
    $hcmis = session('hcmis_login');
?>

<?php if(!empty($hcmis)): ?>
    <?php if(!empty($hcmis['success'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success" role="alert">
                HCMIS login berhasil. Token tersimpan.
                <?php if(!empty($hcmis['token'])): ?>
                    <div class="small text-monospace">Token: <?php echo e(Str::limit($hcmis['token'], 40, '...')); ?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="container mt-3">
            <div class="alert alert-warning" role="alert">
                HCMIS login gagal. <span class="small"><?php echo e(is_string($hcmis['error']) ? $hcmis['error'] : json_encode($hcmis['error'])); ?></span>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/partials/hcmis_status.blade.php ENDPATH**/ ?>