@extends('layouts/admin')
@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Employee Status Renewal
                <small>Pembaharuan Status Karyawan</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Renewal</li>
            </ol>
        </section>

        <section class="content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Success!</h4>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Error!</h4>
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <!-- Filter Box -->
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-filter"></i> Filter Data Renewal</h3>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('renewal.index') }}" method="GET" class="form-inline"
                                style="display: inline-block;">
                                <div class="form-group">
                                    <label for="start_date">Start Date: </label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ $start_date ?? '' }}">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label for="end_date">End Date: </label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ $end_date ?? '' }}">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label for="nik">NIK: </label>
                                    <input type="text" name="nik" id="nik" class="form-control" placeholder="Search NIK..."
                                        value="{{ request('nik') ?? '' }}">
                                </div>
                                <div class="form-group" style="margin-left: 10px;">
                                    <label for="status_approval">Approval Status: </label>
                                    <select name="status_approval" id="status_approval" class="form-control">
                                        <option value="">-- All Status --</option>
                                        <option value="Draft" {{ request('status_approval') == 'Draft' ? 'selected' : '' }}>
                                            Draft</option>
                                        <option value="Submitted" {{ request('status_approval') == 'Submitted' ? 'selected' : '' }}>Submitted</option>
                                        <option value="Supported" {{ request('status_approval') == 'Supported' ? 'selected' : '' }}>Supported</option>
                                        <option value="Proposed" {{ request('status_approval') == 'Proposed' ? 'selected' : '' }}>Proposed</option>
                                        <option value="Approved" {{ request('status_approval') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                                    <i class="fa fa-search"></i> Filter
                                </button>&nbsp;
                                <div class="pull-right">
                                    <button type="button" class="btn btn-success" onclick="showSearchModal()">
                                        <i class="fa fa-plus"></i> New
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-list"></i> Employee Status Renewal List</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="renewal-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Name</th>
                                            <th>Join_Date</th>
                                            <th>Category</th>
                                            <th>Position</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($renewals as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->nik ?? '-' }}</td>
                                                <td>{{ $item->name ?? '-' }}</td>
                                                <td>{{ $item->date_of_join ? date('d/m/Y', strtotime($item->date_of_join)) : '-' }}</td>
                                                <td>{{ $item->category }}</td>
                                                <td>{{ $item->last_position ?? '-' }} to {{ $item->new_position ?? '-' }}</td>
                                                <td>
                                                    <?php 
                                                        if ($item->suggested_status == '') {
                                                            $task='submitted';
                                                            $next_id=$item->suggested_by_id;
                                                            $next_approver=$item->suggested_by_name;
                                                            $teks = "Waiting: " . $item->suggested_by_name;
                                                        } elseif ($item->suggested_status == 2) {
                                                            $task='submitted';
                                                            $next_id=$item->suggested_by_id;
                                                            $next_approver=$item->suggested_by_name;
                                                            $teks = "Pending: " . $item->suggested_by_name;
                                                        } elseif ($item->supported_status == '') {
                                                            $task='supported';
                                                            $next_id=$item->supported_by_id;
                                                            $next_approver=$item->supported_by_name;
                                                            $teks = "Waiting: " . $item->supported_by_name;
                                                        } elseif ($item->supported_status == 2) {
                                                            $task='supported';
                                                            $next_id=$item->supported_by_id;
                                                            $next_approver=$item->supported_by_name;
                                                            $teks = "Pending: " . $item->supported_by_name;
                                                        } elseif ($item->personnel_status == '') {
                                                            $task='personnel';
                                                            $next_id=$item->personnel_id;
                                                            $next_approver=$item->personnel_name;
                                                            $teks = "Waiting: " . $item->personnel_name;
                                                        } elseif ($item->personnel_status == 2) {
                                                            $task='personnel';
                                                            $next_id=$$item->personnel_id;
                                                            $next_approver=$item->personnel_name;
                                                            $teks = "Panding: " . $item->personnel_name;
                                                        } elseif ($item->proposed_status == '') {
                                                            $task='proposed';
                                                            $next_id=$item->proposed_by_id;
                                                            $next_approver=$item->proposed_by_name;
                                                            $teks = "Waiting: " . $item->proposed_by_name;
                                                        } elseif ($item->proposed_status == 2) {
                                                            $task='proposed';
                                                            $next_id=$item->proposed_by_id;
                                                            $next_approver=$item->proposed_by_name;
                                                            $teks = "Pending: " . $item->proposed_by_name;
                                                        } elseif ($item->proposed_status == 1 && $item->category == 'Mutation') {
                                                            $teks = "Approved";
                                                            $next_id='';
                                                            $next_approver='';
                                                        } elseif ($item->approved_status == '' && $item->category == 'Promotion') {
                                                            $task='approved';
                                                            $next_id=$item->approved_by_id;
                                                            $next_approver=$item->approved_by_name;
                                                            $teks = "Waiting: " . $item->approved_by_name;
                                                        } elseif ($item->approved_status == 2) {
                                                            $task='approved';
                                                            $next_id=$item->approved_by_id;
                                                            $next_approver=$item->approved_by_name;
                                                            $teks = "Pending: " . $item->approved_by_name;
                                                        } elseif ($item->approved_status == 1) {
                                                            $teks = "Approved";
                                                            $next_id='';
                                                            $next_approver='';
                                                        }
                                                    ?>
                                                    <div class="row" style="margin: 0;">
                                                    <div class="col-md-12" style="padding: 2px 0; margin-bottom: 5px; border-bottom: 1px solid #eee;">
                                                    <div
                                                    style="display: flex; align-items: center; justify-content: space-between;">
                                                    <div style="display: flex; gap: 5px;">
                                                    @if($item->suggested_status == '1')
                                                    <span class="label label-success"
                                                    style="cursor: pointer;">1</span>
                                                    @elseif($item->suggested_status == '2')
                                                    <span class="label label-warning"
                                                    style="cursor: pointer;">1</span>
                                                    @else
                                                    <span class="label label-default"
                                                    style="cursor: pointer;">1</span>
                                                    @endif
                                                    @if($item->supported_status == '1')
                                                    <span class="label label-success"
                                                    style="cursor: pointer;">2</span>
                                                    @elseif($item->supported_status == '2')
                                                    <span class="label label-warning"
                                                    style="cursor: pointer;">2</span>
                                                    @else
                                                    <span class="label label-default"
                                                    style="cursor: pointer;">2</span>
                                                    @endif
                                                    @if($item->personnel_status == '1')
                                                    <span class="label label-success"
                                                    style="cursor: pointer;">3</span>
                                                    @elseif($item->personnel_status == '2')
                                                    <span class="label label-warning"
                                                    style="cursor: pointer;">3</span>
                                                    @else
                                                    <span class="label label-default"
                                                    style="cursor: pointer;">3</span>
                                                    @endif
                                                    @if($item->proposed_status == '1')
                                                    <span class="label label-success"
                                                    style="cursor: pointer;">4</span>
                                                    @elseif($item->proposed_status == '2')
                                                    <span class="label label-warning"
                                                    style="cursor: pointer;">4</span>
                                                    @else
                                                    <span class="label label-default"
                                                    style="cursor: pointer;">4</span>
                                                    @endif
                                                    @if($item->category == 'Promotion')
                                                    @if($item->approved_status == '1')
                                                    <span class="label label-success"
                                                    style="cursor: pointer;">5</span>
                                                    @elseif($item->approved_status == '2')
                                                    <span class="label label-warning"
                                                    style="cursor: pointer;">5</span>
                                                    @else
                                                    <span class="label label-default"
                                                    style="cursor: pointer;">5</span>
                                                    @endif
                                                    @endif
                                                    </div>
                                                    </div>
                                                    </div>
                                                </td>
                                                <td style="min-width:200px;">
                                                    <div class="row" style="margin: 0;">
                                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                                            @if($teks == 'Approved')
                                                                <span class="label label-success" style="cursor: pointer;">Approved</span>
                                                            @else
                                                                <span style="font-size: 11px; width: 200px;">{{ $teks }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('renewal.show', $item->id) }}" class="btn btn-xs btn-info" title="View"><i class="fa fa-eye"></i></a>
                                                        <button type="button" class="btn btn-xs btn-success" title="Approval" data-task="{{ $task }}" data-nextapprover="{{ $next_approver }}" data-nextid="{{ $next_id }}" onclick="showApprovalModal(this, {{ $item->id }})"><i class="fa fa-check-circle"></i></button>
                                                        <a href="{{ route('renewal.print', $item->id) }}" class="btn btn-xs btn-primary" title="Print" target="_blank"><i class="fa fa-print"></i></a>
                                                        <button type="button" class="btn btn-xs btn-danger" title="Delete" onclick="deleteRenewal({{ $item->id }})"><i class="fa fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if(count($renewals) == 0)
                                            <tr>
                                                <td colspan="11" class="text-center">No data available</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Search Employee -->
    <div class="modal fade" id="modal-search" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Cari Karyawan</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>NIK / Nama Karyawan</label>
                        <input type="text" id="search_employee" class="form-control" placeholder="Masukkan NIK atau Nama">
                        <div id="employee_result" style="margin-top: 10px;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Approval -->
    <div class="modal fade" id="modal-approval" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Approval Employee Status Renewal</h4>
                </div>
                <form id="approval-form" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="renewal_id" id="approval_renewal_id">
                        <div class="form-group">
                            <label>Approver Name</label>
                            <input type="text" name="approval_stage_show" id="approval_stage_show" class="form-control" required disabled>
                            <input type="hidden" name="approval_stage" id="approval_stage" class="form-control" required>
                            <input type="hidden" name="nextid" id="nextid" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Approver Name</label>
                            <input type="text" id="approver_name_show" class="form-control" required disabled>
                            <input type="hidden" name="approver_name" id="approver_name">
                        </div>
                        <div class="form-group">
                            <label>Action</label>
                            <select name="approval_action" id="approval_action" class="form-control" required>
                                <option value="1">Approve</option>
                                <option value="2">Pending</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Approval Date</label>
                            <input type="date" id="approval_date_show" class="form-control"
                                value="{{ date('Y-m-d') }}" required disabled>
                            <input type="hidden" name="approval_date" id="approval_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submit_approval">Submit Approval</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('Scripts')
    <script>
        $(document).ready(function () {
            $('#renewal-table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'print'
                ]
            });
        });

        // Search Employee for New Renewal
        function showSearchModal() {
            $('#modal-search').modal('show');
        }

        $('#search_employee').on('keyup', function () {
            var keyword = $(this).val();
            if (keyword.length >= 2) {
                $.ajax({
                    url: '{{ route("renewal.search") }}',
                    type: 'GET',
                    data: { keyword: keyword },
                    success: function (response) {
                        var html = '';
                        if (response.length > 0) {
                            html += '<div class="list-group">';
                            $.each(response, function (key, emp) {
                                html += '<a href="{{ route("renewal.create") }}?nik=' + emp.nik + '" class="list-group-item list-group-item-action">';
                                html += '<strong>' + emp.nik + '</strong> - ' + emp.name + '<br>';
                                html += '<small>' + (emp.position || '-') + ' | ' + (emp.department || '-') + '</small>';
                                html += '</a>';
                            });
                            html += '</div>';
                        } else {
                            html = '<div class="alert alert-warning">Karyawan tidak ditemukan</div>';
                        }
                        $('#employee_result').html(html);
                    }
                });
            } else {
                $('#employee_result').html('');
            }
        });

        // Approval Modal
        function showApprovalModal(element, id) {
            $('#approval_renewal_id').val(id);
            var nextid = $(element).data('nextid');
            var task = $(element).data('task');
            var nextapprover = $(element).data('nextapprover');
            var taskCapitalized = task.substring(0, 1).toUpperCase() + task.substring(1);
            $('#nextid').val(nextid);
            $('#approval_stage').val(task);
            $('#approval_stage_show').val(taskCapitalized);
            $('#approver_name').val(nextapprover);
            $('#approver_name_show').val(nextapprover);
            var userid="{{ $user_id }}";
            if(userid==nextid){
                document.getElementById('submit_approval').disabled = false;
            }else{
                document.getElementById('submit_approval').disabled = true;
            }
            $('#modal-approval').modal('show');
        }

        $('#approval-form').on('submit', function (e) {
            e.preventDefault();
            var id = $('#approval_renewal_id').val();
            var formData = $(this).serialize();

            $.ajax({
                url: '{{ route("renewal.approve", "") }}/' + id,
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        $('#modal-approval').modal('hide');
                        location.reload();
                    } else {
                        alert('A');
                        toastr.error(response.message, 'Error!');
                    }
                },
                error: function (xhr) {
                    toastr.error('Terjadi kesalahan, silakan coba lagi.', 'Error!');
                }
            });
        });

        // Delete Renewal
        function deleteRenewal(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini? Data yang sudah dihapus tidak dapat dikembalikan!')) {
                $.ajax({
                    url: '{{ route("renewal.destroy", "") }}/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Gagal: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        alert('Terjadi kesalahan, silakan coba lagi.');
                    }
                });
            }
        }
        // Initialize toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    </script>

@endsection