

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">HCMIS Data - Test Console</div>

                <div class="card-body">
                    <form id="loginForm">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-control" value="<?php echo e(config('hcmis.username')); ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" class="form-control" value="<?php echo e(config('hcmis.password')); ?>">
                        </div>
                        <button type="button" id="btnLogin" class="btn btn-primary">Login (proxy)</button>
                    </form>

                    <hr>

                    <div>
                        <button id="btnList" class="btn btn-success">Get Employees (datatables)</button>
                        <button id="btnStore" class="btn btn-warning">Store Sample Employee</button>
                        <a href="/hcmis/employees" class="btn btn-info">Open Employee View</a>
                    </div>

                    <hr>

                    <h5>Response</h5>
                    <pre id="output" style="height:300px;overflow:auto;background:#f8f9fa;padding:10px;border:1px solid #ddd;"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function showOutput(obj){
    document.getElementById('output').textContent = JSON.stringify(obj, null, 2);
}

function showError(message) {
    showOutput({ error: true, message });
}

document.addEventListener('DOMContentLoaded', function() {
    const btnLogin = document.getElementById('btnLogin');
    const btnList = document.getElementById('btnList');
    const btnStore = document.getElementById('btnStore');

    if (!btnLogin || !btnList || !btnStore) {
        showError('Buttons not found in DOM.');
        return;
    }

    btnLogin.addEventListener('click', async function() {
        try {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const res = await fetch('/api/hcmis/login', {
                method: 'POST',
                headers: {'Content-Type':'application/json','Accept':'application/json'},
                body: JSON.stringify({email,password})
            });
            const data = await res.json();
            showOutput(data);
        } catch (err) {
            showError(err.message || 'Login request failed');
            console.error(err);
        }
    });

    btnList.addEventListener('click', async function() {
        try {
            const res = await fetch('/api/hcmis/employees/datatables', {
                method: 'POST',
                headers: {'Content-Type':'application/json','Accept':'application/json'},
                body: JSON.stringify({rows:5,page:1})
            });
            const data = await res.json();
            showOutput(data);
        } catch (err) {
            showError(err.message || 'Datatables request failed');
            console.error(err);
        }
    });

    btnStore.addEventListener('click', async function() {
        try {
            const payload = {
                company_code: '<?php echo e(config('hcmis.company_code')); ?>',
                organization_id: 1,
                employee_number: '191015-002',
                full_name: 'HARI RISNAWAN',
                email: '',
                place_of_birth: '',
                date_of_birth: '',
                gender: '',
                contract_type: 'Permanent',
                employment_status_id: 1,
                start_date: '2015-10-19',
                end_date: '2017-04-19',
                job_position_id: 1,
                division_id: 3,
                department_id: 7,
                section_id: 2,
                level_id: 6
            };
            const res = await fetch('/api/hcmis/employees/store', {
                method: 'POST',
                headers: {'Content-Type':'application/json','Accept':'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            showOutput(data);
        } catch (err) {
            showError(err.message || 'Store request failed');
            console.error(err);
        }
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/hcmis/index.blade.php ENDPATH**/ ?>