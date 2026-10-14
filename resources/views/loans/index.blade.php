@extends('layouts/payroll_recap')
@section('Contents')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary" style="background:#FFF;">
                    <div class="box-header">
                        <h3 class="box-title">Loan Management</h3>
                        <div class="pull-right">
                            <a href="{{ route('loans.create') }}" class="btn btn-primary btn-md">
                                <i class="fa fa-plus"></i> &nbsp;Add New Loan
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{ session('error') }}
                            </div>
                        @endif

                        <table id="tables" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:50px;">No</th>
                                    <th style="width:90px;">NIK</th>
                                    <th style="width:200px;">EMPLOYEE</th>
                                    <th style="width:50px;">POSITION</th>
                                    <th style="width:50px;">DEPT</th>
                                    <th style="width:120px;">LOANS</th>
                                    <th style="width:120px;">PER_MONTH</th>
                                    <th style="width:100px;">INSTALLMENTS</th>
                                    <th style="width:120px;">REMAINING</th>
                                    <th style="width:150px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $no=0;?>
                            @foreach($loans as $dt)
                                <tr>
                                    <td>
                                        <?php $no++;?>
                                        {{$no}}
                                    </td>
                                    <td>{{$dt->NIK}}</td>
                                    <td>{{$dt->nama_karyawan}}</td>
                                    <td>{{$dt->jabatan}}</td>
                                    <td>{{$dt->department}}</td>
                                    <td align="right">{{ number_format($dt->amount_loan, 0, ',', '.') }}</td>
                                    <td align="right">{{ number_format($dt->amount_per_month, 0, ',', '.') }}</td>
                                    <td align="center">
                                        <span class="label label-info">{{ $dt->paid_installments ?? 0 }}/{{ $dt->total_installments ?? 0 }}</span>
                                    </td>
                                    <td align="right">
                                        <span class="label label-warning">{{ number_format($dt->remaining_balance ?? 0, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('loans.show', $dt->id) }}" class="btn btn-primary btn-xs" title="View Details">
                                                <i class="fa fa-folder"></i>
                                            </a>
                                            @if($dt->progress=='0')
                                                <button class="btn btn-danger btn-xs delete-loan" data-id="{{ $dt->id }}" data-name="{{ $dt->nama_karyawan }}" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
@endsection

@section('Modals')
    <!-- Delete Modal -->
    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog box box-danger" style="width:400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Confirmation</h4>
                </div>
                <div class="modal-body">
                    <b>Delete loan for </b> <b id="delname"></b> ?
                    <input type="hidden" id="delid">
                    <p class="text-warning"><small>This will also delete all related loan details.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left delete-confirm" data-dismiss="modal">Yes, Delete</button>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('Scripts')
    <script>
        $(document).ready(function() {

            // Delete loan confirmation
            $(document).on('click', '.delete-loan', function() {
                $('#delid').val($(this).data('id'));
                $('#delname').text($(this).data('name'));
                $('#modal-delete').modal('show');
            });

            // Confirm delete
            $(document).on('click', '.delete-confirm', function(e) {
                e.preventDefault();
                
                var id = $('#delid').val();
                
                if (!id) {
                    alert('ID not found!');
                    return;
                }
                
                if (!confirm('Are you sure you want to delete this loan?')) {
                    return;
                }
                
                var url = "{{ route('loans.destroy', '') }}/" + id;
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Failed to delete loan. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                        console.error('Error details:', xhr.responseText);
                    }
                });
            });

            // Auto-hide alerts
            setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 5000);
        });
    </script>
@endsection