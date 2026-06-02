@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Outside Assignment<small>Edit Assignment</small></h1>
		</section>

		<section class="content">
		<div class="row">
            <form role="form" action="{{ url('/outside/'.$header->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="col-md-4">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Header Information</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="book_id">Book ID</label>
                                <input type="number" class="form-control" name="book_id" value="{{ old('book_id', $header->book_id) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal', $header->tanggal) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="nopol">No Polisi (License Plate)</label>
                                <input type="text" class="form-control" name="nopol" value="{{ old('nopol', $header->nopol) }}" required placeholder="e.g. B 1234 ABC">
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-warning btn-block">Update Assignment</button>
                            <a href="{{ url('/outside') }}" class="btn btn-default btn-block">Cancel</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Select Employees</h3>
                        </div>
                        <div class="box-body" style="max-height: 600px; overflow-y: auto;">
                            <table id="empTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:30px;"><input type="checkbox" id="checkAll"></th>
                                        <th>NIK</th>
                                        <th>Name</th>
                                        <th>Dept</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $emp)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="employees[]" value="{{ $emp->id_employee }}" 
                                                   class="emp-checkbox" {{ in_array($emp->id_employee, $details) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $emp->NIK }}</td>
                                        <td>{{ $emp->nama_karyawan }}</td>
                                        <td>{{ $emp->department }}</td>
                                        <td>{{ $emp->jabatan }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
		</div>
		</section>
  	</div>
@endsection

@section('Scripts')
<script>
    $(document).ready(function() {
        var table = $('#empTable').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false
        });

        $('#checkAll').on('click', function() {
            var rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        $('form').on('submit', function(e) {
            var form = this;
            table.$('input.emp-checkbox:checked').each(function() {
                if(!$.contains(document, this)) {
                    $(form).append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'employees[]')
                            .val($(this).val())
                    );
                }
            });
        });
    });
</script>
@endsection
