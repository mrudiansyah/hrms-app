

<?php $__env->startSection('Contents'); ?>
    <style>
        /* Scoped Organization Chart CSS Tree */
        .org-chart-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: auto;
            padding: 40px 30px;
            background: #f8f9fa;
            border-radius: 8px;
            min-height: 500px;
            cursor: grab;
            user-select: none;
        }

        .org-chart-wrapper:active {
            cursor: grabbing;
        }

        .org-chart-wrapper .org-tree-chart,
        .org-chart-wrapper .org-tree-chart ul,
        .org-chart-wrapper .org-tree-chart li {
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
            position: relative !important;
        }

        .org-chart-wrapper .org-tree-chart {
            display: table;
            margin: 0 auto !important;
            text-align: center;
        }

        .org-chart-wrapper .org-tree-chart ul {
            display: flex;
            justify-content: center;
            padding-top: 25px;
            position: relative;
            transition: all 0.3s;
        }

        /* Hidden branch class overrides display: flex */
        .org-chart-wrapper .org-tree-chart ul.hidden-branch {
            display: none !important;
        }

        .org-chart-wrapper .org-tree-chart li {
            float: left;
            text-align: center;
            list-style-type: none;
            position: relative;
            padding: 25px 28px 0 28px !important;
            transition: all 0.3s;
        }

        /* Connector lines - Top & Horizontal lines for children */
        .org-chart-wrapper .org-tree-chart li::before,
        .org-chart-wrapper .org-tree-chart li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 2px solid #3c8dbc;
            width: 50%;
            height: 25px;
        }

        .org-chart-wrapper .org-tree-chart li::after {
            right: auto;
            left: 50%;
            border-left: 2px solid #3c8dbc;
        }

        .org-chart-wrapper .org-tree-chart li:only-child::after,
        .org-chart-wrapper .org-tree-chart li:only-child::before {
            display: none;
        }

        .org-chart-wrapper .org-tree-chart li:only-child {
            padding-top: 0 !important;
        }

        .org-chart-wrapper .org-tree-chart li:first-child::before,
        .org-chart-wrapper .org-tree-chart li:last-child::after {
            border: 0 none;
        }

        .org-chart-wrapper .org-tree-chart li:last-child::before {
            border-right: 2px solid #3c8dbc;
            border-radius: 0 5px 0 0;
        }

        .org-chart-wrapper .org-tree-chart li:first-child::after {
            border-radius: 5px 0 0 0;
        }

        .org-chart-wrapper .org-tree-chart ul ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #3c8dbc;
            width: 0;
            height: 25px;
        }

        /* Toggle Button Wrapper & Vertical Connector Line */
        .org-chart-wrapper .btn-toggle-wrapper {
            position: relative;
            padding-top: 18px;
            padding-bottom: 5px;
        }

        .org-chart-wrapper .btn-toggle-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            border-left: 2px solid #3c8dbc;
            width: 0;
            height: 100%;
            z-index: 1;
        }

        .org-chart-wrapper .tree-toggle-btn {
            position: relative;
            z-index: 2;
            padding: 4px 14px;
            font-size: 11px;
            border-radius: 14px;
            background-color: #ffffff;
            border: 1.5px solid #3c8dbc;
            font-weight: 600;
            color: #3c8dbc;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .org-chart-wrapper .tree-toggle-btn:hover {
            background-color: #3c8dbc;
            color: #ffffff;
        }

        /* Employee Card Box */
        .org-chart-wrapper .node-box {
            display: inline-block;
            width: 210px;
            padding: 14px 12px;
            background: #ffffff;
            border: 2px solid #3c8dbc;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            background-clip: padding-box;
            cursor: pointer;
            z-index: 3;
        }

        .org-chart-wrapper .node-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(60, 141, 188, 0.25);
            border-color: #00a65a;
        }

        .org-chart-wrapper .node-box.highlight {
            border-color: #f39c12 !important;
            box-shadow: 0 0 15px rgba(243, 156, 18, 0.6) !important;
            background-color: #fffdf5 !important;
        }

        /* Portrait Rectangle Avatar Box */
        .org-chart-wrapper .node-avatar-box {
            width: 65px;
            height: 82px;
            border-radius: 6px;
            background: #f0f4f8;
            border: 2px solid #3c8dbc;
            margin: 0 auto 8px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3c8dbc;
            font-size: 42px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .org-chart-wrapper .node-name {
            font-weight: 700;
            font-size: 13px;
            color: #2c3e50;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .org-chart-wrapper .node-badges {
            margin-bottom: 6px;
        }

        .org-chart-wrapper .node-badge-dept {
            background-color: #3c8dbc;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            display: inline-block;
        }

        .org-chart-wrapper .node-position {
            font-size: 11px;
            color: #555;
            font-style: italic;
            margin-bottom: 8px;
            min-height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1.2;
        }

        .org-chart-wrapper .sub-counts {
            border-top: 1px solid #eeeeee;
            padding-top: 6px;
            display: flex;
            justify-content: space-around;
            font-size: 10px;
        }

        .org-chart-wrapper .count-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .org-chart-wrapper .count-number {
            font-weight: 700;
            font-size: 12px;
        }

        .org-chart-wrapper .count-label {
            font-size: 9px;
            color: #777;
        }

        .org-chart-wrapper .badge-direct {
            color: #0073b7;
        }

        .org-chart-wrapper .badge-total {
            color: #00a65a;
        }

        /* Modal Photo Portrait Rectangle Style */
        .modal-photo-img {
            width: 130px;
            height: 165px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #3c8dbc;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin: 0 auto 15px auto;
            display: block;
        }
    </style>

    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <h1>
                Struktur Organisasi
                <small>Hierarki Employee</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="/Dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active"><i class="fa fa-users"></i> Structure</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-sitemap text-blue"></i>
                            <h3 class="box-title">Bagian Organisasi Karyawan</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" id="btn-zoom-in" title="Zoom In"><i
                                        class="fa fa-search-plus"></i></button>
                                <button type="button" class="btn btn-box-tool" id="btn-zoom-out" title="Zoom Out"><i
                                        class="fa fa-search-minus"></i></button>
                                <button type="button" class="btn btn-box-tool" id="btn-zoom-reset" title="Reset Zoom"><i
                                        class="fa fa-refresh"></i></button>
                            </div>
                        </div>

                        <div class="box-body">
                            <!-- Filters & Controls Bar -->
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                        <input type="text" id="search-input" class="form-control"
                                            placeholder="Cari Nama, NIK, atau Jabatan...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select id="dept-filter" class="form-control">
                                        <option value="">-- Semua Department --</option>
                                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($dept->dept_code); ?>"><?php echo e($dept->dept_name); ?>

                                                (<?php echo e($dept->dept_code); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-5 text-right">
                                    <a href="?refresh=1" class="btn btn-default btn-sm"
                                        title="Refresh data dari database"><i class="fa fa-refresh"></i> Refresh Cache</a>
                                    <button type="button" class="btn btn-default btn-sm" id="btn-expand-all"><i
                                            class="fa fa-plus-square-o"></i> Expand All</button>
                                    <button type="button" class="btn btn-default btn-sm" id="btn-collapse-all"><i
                                            class="fa fa-minus-square-o"></i> Collapse All</button>
                                </div>
                            </div>

                            <!-- Organization Chart Render Area -->
                            <div class="org-chart-wrapper" id="zoom-container">
                                <div id="tree-content" style="transform-origin: top center; transition: transform 0.2s;">
                                    <?php if(count($tree) > 0): ?>
                                        <div class="org-tree-chart">
                                            <ul>
                                                <?php $__currentLoopData = $tree; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rootNode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo $__env->make('page.admin.structure.node', ['node' => $rootNode, 'level' => 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info text-center" style="margin: 40px auto; max-width: 400px;">
                                            <i class="fa fa-info-circle fa-2x"></i><br><br>
                                            Belum ada data hierarki karyawan aktif.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Employee Detail & Photo -->
    <div class="modal fade" id="modal-employee-detail" tabindex="-1" role="dialog" aria-labelledby="modalEmpTitle">
        <div class="modal-dialog" role="document" style="width: 420px;">
            <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
                <div class="modal-header bg-primary" style="color: white;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalEmpTitle"><i class="fa fa-user"></i> Detail Karyawan</h4>
                </div>
                <div class="modal-body" style="padding: 25px;">
                    <div id="modal-loader" class="text-center" style="padding: 30px;">
                        <i class="fa fa-spinner fa-spin fa-3x text-blue"></i>
                        <p style="margin-top: 10px; color: #777;">Memuat data karyawan...</p>
                    </div>
                    <div id="modal-content-body" style="display: none;">
                        <div class="text-center">
                            <img id="modal-emp-photo" src="<?php echo e(asset('/assets/dist/img/user.jpg')); ?>" class="modal-photo-img"
                                alt="Foto Karyawan">
                            <h4 id="modal-emp-name" style="font-weight: 700; margin-bottom: 2px; color: #2c3e50;">-</h4>
                            <p id="modal-emp-position" style="font-style: italic; color: #7f8c8d; margin-bottom: 15px;">-
                            </p>
                        </div>
                        <table class="table table-bordered table-striped" style="font-size: 13px;">
                            <tr>
                                <th style="width: 35%;">NIK</th>
                                <td id="modal-emp-nik">-</td>
                            </tr>
                            <tr>
                                <th>Departemen</th>
                                <td id="modal-emp-dept">-</td>
                            </tr>
                            <tr>
                                <th>Direct Leader</th>
                                <td id="modal-emp-leader">-</td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td id="modal-emp-gender">-</td>
                            </tr>
                            <tr>
                                <th>Tanggal Masuk</th>
                                <td id="modal-emp-join">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('Scripts'); ?>
    <script>
        $(document).ready(function () {
            var currentZoom = 1;

            // Mouse Click-and-Drag Pan (Drag to Scroll)
            const container = document.querySelector('.org-chart-wrapper');
            let isDown = false;
            let startX, startY, scrollLeft, scrollTop;

            $(container).on('mousedown', function (e) {
                if ($(e.target).closest('.node-box').length || $(e.target).closest('.tree-toggle-btn').length || $(e.target).closest('button').length) {
                    return;
                }
                isDown = true;
                container.style.cursor = 'grabbing';
                startX = e.pageX - container.offsetLeft;
                startY = e.pageY - container.offsetTop;
                scrollLeft = container.scrollLeft;
                scrollTop = container.scrollTop;
            });

            $(container).on('mouseleave mouseup', function () {
                isDown = false;
                container.style.cursor = 'grab';
            });

            $(container).on('mousemove', function (e) {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const y = e.pageY - container.offsetTop;
                const walkX = (x - startX) * 1.5;
                const walkY = (y - startY) * 1.5;
                container.scrollLeft = scrollLeft - walkX;
                container.scrollTop = scrollTop - walkY;
            });

            // Zoom In
            $('#btn-zoom-in').click(function () {
                if (currentZoom < 1.6) {
                    currentZoom += 0.15;
                    applyZoom();
                }
            });

            // Zoom Out
            $('#btn-zoom-out').click(function () {
                if (currentZoom > 0.4) {
                    currentZoom -= 0.15;
                    applyZoom();
                }
            });

            // Reset Zoom
            $('#btn-zoom-reset').click(function () {
                currentZoom = 1;
                applyZoom();
            });

            function applyZoom() {
                $('#tree-content').css('transform', 'scale(' + currentZoom + ')');
            }

            // Expand All
            $('#btn-expand-all').click(function () {
                $('.org-tree-chart ul').removeClass('hidden-branch');
                $('.tree-toggle-btn i').removeClass('fa-plus').addClass('fa-minus');
            });

            // Collapse All (keep level 1 & 2 visible, collapse level 3+)
            $('#btn-collapse-all').click(function () {
                $('.org-tree-chart li').each(function () {
                    var $childUl = $(this).children('ul');
                    var $btn = $(this).children('.btn-toggle-wrapper').find('.tree-toggle-btn');
                    var isTopLevel = $(this).parents('ul').length <= 1;

                    if ($childUl.length > 0) {
                        if (isTopLevel) {
                            $childUl.removeClass('hidden-branch');
                            $btn.find('i').removeClass('fa-plus').addClass('fa-minus');
                        } else {
                            $childUl.addClass('hidden-branch');
                            $btn.find('i').removeClass('fa-minus').addClass('fa-plus');
                        }
                    }
                });
            });

            // Toggle Node Children (+ / - button)
            $(document).on('click', '.tree-toggle-btn', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var $parentLi = $(this).closest('li');
                var $childUl = $parentLi.children('ul');

                if ($childUl.length > 0) {
                    $childUl.toggleClass('hidden-branch');
                    if ($childUl.hasClass('hidden-branch')) {
                        $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
                    } else {
                        $(this).find('i').removeClass('fa-plus').addClass('fa-minus');
                    }
                }
            });

            // Click Node Box to Open Detail Modal
            $(document).on('click', '.node-box', function (e) {
                var empId = $(this).data('id');
                if (!empId) return;

                $('#modal-loader').show();
                $('#modal-content-body').hide();
                $('#modal-employee-detail').modal('show');

                $.ajax({
                    url: '/Structure/Detail/' + empId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            var d = res.data;
                            $('#modal-emp-name').text(d.employee_name);
                            $('#modal-emp-position').text(d.position_name || '-');
                            $('#modal-emp-nik').text(d.NIK);
                            $('#modal-emp-dept').text((d.dept_name || '') + ' (' + (d.dept_code || '-') + ')');
                            $('#modal-emp-leader').text(d.leader_name || '-');
                            $('#modal-emp-gender').text(d.gender || '-');
                            $('#modal-emp-join').text(d.join_date || '-');

                            if (d.photo) {
                                $('#modal-emp-photo').attr('src', d.photo);
                            } else {
                                $('#modal-emp-photo').attr('src', '<?php echo e(asset("/assets/dist/img/user.jpg")); ?>');
                            }

                            $('#modal-loader').hide();
                            $('#modal-content-body').fadeIn(200);
                        }
                    },
                    error: function () {
                        $('#modal-loader').html('<div class="alert alert-danger">Gagal mengambil data karyawan.</div>');
                    }
                });
            });

            // Search Filter
            $('#search-input').on('keyup', function () {
                var term = $(this).val().toLowerCase().trim();
                $('.node-box').removeClass('highlight');

                if (term === '') {
                    return;
                }

                $('.node-box').each(function () {
                    var name = $(this).data('name') ? $(this).data('name').toString().toLowerCase() : '';
                    var nik = $(this).data('nik') ? $(this).data('nik').toString().toLowerCase() : '';
                    var pos = $(this).data('position') ? $(this).data('position').toString().toLowerCase() : '';

                    if (name.includes(term) || nik.includes(term) || pos.includes(term)) {
                        $(this).addClass('highlight');
                        $(this).parents('ul').removeClass('hidden-branch');
                        $(this).parents('li').children('.btn-toggle-wrapper').find('.tree-toggle-btn i').removeClass('fa-plus').addClass('fa-minus');
                    }
                });
            });

            // Department Filter
            $('#dept-filter').on('change', function () {
                var selectedDept = $(this).val();
                $('.node-box').removeClass('highlight');

                if (!selectedDept) {
                    return;
                }

                $('.node-box').each(function () {
                    var dept = $(this).data('dept') ? $(this).data('dept').toString() : '';
                    if (dept === selectedDept) {
                        $(this).addClass('highlight');
                        $(this).parents('ul').removeClass('hidden-branch');
                        $(this).parents('li').children('.btn-toggle-wrapper').find('.tree-toggle-btn i').removeClass('fa-plus').addClass('fa-minus');
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/structure/index.blade.php ENDPATH**/ ?>