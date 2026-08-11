

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?php echo e($Judul ?? 'HCMIS Employees'); ?></span>
                    <a href="/hcmis" class="btn btn-sm btn-outline-secondary">Kembali</a>
                </div>

                <div class="card-body">
                    <div class="alert alert-info" id="employeeStatus" role="status">
                        Klik tombol untuk memuat data karyawan dari HCMIS.
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <button id="btnLoadEmployees" class="btn btn-primary">Load Employees</button>
                    </div>

                    <div class="table-responsive">
                        <table id="hcmisEmployeesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Employee Number</th>
                                    <th>Full Name</th>
                                    <th>Contract Type</th>
                                    <th>Gender</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnLoadEmployees');
    const status = document.getElementById('employeeStatus');
    const tableBody = document.querySelector('#hcmisEmployeesTable tbody');

    btn.addEventListener('click', async function () {
        btn.disabled = true;
        btn.textContent = 'Loading...';
        status.className = 'alert alert-info';
        status.textContent = 'Memuat data...';
        tableBody.innerHTML = '';

        try {
            const response = await fetch('/api/hcmis/employees/datatables?rows=100&page=1', {
                method: 'GET',
                headers: {'Accept': 'application/json'}
            });

            const data = await response.json();
            const rows = Array.isArray(data?.data) ? data.data : (Array.isArray(data?.records) ? data.records : []);

            if (!rows.length) {
                status.className = 'alert alert-warning';
                status.textContent = 'Tidak ada data karyawan yang dikembalikan.';
                return;
            }

            tableBody.innerHTML = rows.map((item, index) => {
                const employeeNumber = item.employee_number || item.employeeNumber || item.emp_no || '-';
                const fullName = item.full_name || item.fullName || item.name || '-';
                const contractType = item.contract_type || item.contractType || item.contract || '-';
                const gender = item.gender || '-';
                const startDate = item.start_date || item.startDate || '-';
                const endDate = item.end_date || item.endDate || '-';

                return `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${employeeNumber}</td>
                        <td>${fullName}</td>
                        <td>${contractType}</td>
                        <td>${gender}</td>
                        <td>${startDate}</td>
                        <td>${endDate}</td>
                    </tr>
                `;
            }).join('');

            status.className = 'alert alert-success';
            status.textContent = `Berhasil memuat ${rows.length} data karyawan.`;
        } catch (error) {
            status.className = 'alert alert-danger';
            status.textContent = error.message || 'Gagal memuat data.';
        } finally {
            btn.disabled = false;
            btn.textContent = 'Load Employees';
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/hcmis/employee.blade.php ENDPATH**/ ?>