<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Overtime</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; margin-bottom: 24px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        td:last-child { text-align: right; }
        .total { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
    </style>
</head>
<body>
    <h1>SLIP OVERTIME</h1>
    <div class="subtitle"><?php echo e($period); ?><?php echo e($category !== 'Previous Slip' ? ' · ' . $category : ''); ?></div>

    <table>
        <tr><td>Nama karyawan</td><td><?php echo e($employee->employee_name); ?></td></tr>
        <tr><td>NIK</td><td><?php echo e($employee->NIK); ?></td></tr>
        <?php if($startDate && $endDate): ?>
            <tr><td>Periode kerja</td><td><?php echo e($startDate); ?> – <?php echo e($endDate); ?></td></tr>
        <?php endif; ?>
        <tr><td>Pendapatan overtime</td><td>Rp <?php echo e(number_format($earnings, 0, ',', '.')); ?></td></tr>
        <tr><td>Rapel</td><td>Rp <?php echo e(number_format($rapel, 0, ',', '.')); ?></td></tr>
        <tr><td>Potongan PPh 21</td><td>Rp <?php echo e(number_format($deductions, 0, ',', '.')); ?></td></tr>
        <tr class="total"><td>Total diterima</td><td>Rp <?php echo e(number_format($received, 0, ',', '.')); ?></td></tr>
    </table>
</body>
</html>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views\page_ess\mobile_overtime_slip.blade.php ENDPATH**/ ?>