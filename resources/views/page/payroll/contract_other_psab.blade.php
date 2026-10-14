@extends('layouts/payroll_other')
@section('Contents')
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<h3 class="box-title">{{$juduls}}</h3>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>NO</th>
									<th>NIK</th>
									<th>EMPLOYEE_NAME</th>
									<th>JOIN_DATE</th>
									<th>DEPT</th>
									<th>CC_CODE</th>
									<th>CONTRACT</th>
									<th>CATEGORY</th>
									<th>DAILY_SALARY</th>
								</tr>
							</thead>
							<tbody>
								<?php $no=0;?>
								@foreach($tb_salary_contract as $dt)
									<tr>
										<td>{{ ++$no }}</td>
										<td>{{ $dt->NIK ?? '' }}</td>
										<td>{{ $dt->nama_karyawan ?? $dt->employee_name ?? '' }}</td>
										<td>{{ isset($dt->join_date) ? date('d-M-Y', strtotime($dt->join_date)) : '' }}</td>
										<td>{{ $dt->department ?? '' }}</td>
										<td>{{ $dt->cc_code ?? '' }}</td>
										<td>{{ $dt->contract_name ?? '' }}</td>
										<td>{{ $dt->kategori ?? '' }}</td>
										<td>
                                            {{ isset($dt->amount) ? number_format($dt->amount, 0) : '0' }}
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary btn-xs edit-modal" data-id="{{$dt->id}}" data-nama="{{ $dt->nama_karyawan ?? $dt->employee_name ?? '' }}" data-kategori="{{$dt->kategori}}"><i class="fa fa-edit"></i></button>
                                            </div>
                                        </td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
@section('Modals')
    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog" style="width:400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Contract Other</h4>
                </div>
                <div class="modal-body">
                    <form id="form-edit">
                        <input type="hidden" id="edit-id">
                        <div class="form-group">
                            <label>Employee Name</label>
                            <input type="text" class="form-control" id="edit-nama" readonly>
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select class="form-control" id="edit-kategori">
                                <option value="PSAB_5Days">PSAB_5Days</option>
                                <option value="PSAB_6Days">PSAB_6Days</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="edit-status">
                                <option value="1">Active</option>
                                <option value="0">Non-Active</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-save">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('Scripts')
    <script>
        $(document).on('click', '.edit-modal', function() {
            $('#edit-id').val($(this).data('id'));
            $('#edit-nama').val($(this).data('nama'));
            $('#edit-kategori').val($(this).data('kategori'));
            $('#edit-status').val('1'); 
            $('#modal-edit').modal('show');
        });

        $('#btn-save').on('click', function() {
            var id = $('#edit-id').val();
            var kategori = $('#edit-kategori').val();
            var status = $('#edit-status').val();
            
            $.ajax({
                url: '{{$site}}/ContractOther/Update',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    kategori: kategori,
                    status: status
                },
                success: function(response) {
                    if (response.status == 'Success') {
                        alert('Update Successful');
                        window.location.reload();
                    } else {
                        alert('Update Failed: ' + (response.status || response));
                    }
                }
            });
        });
    </script>
@endsection
