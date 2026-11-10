@extends('layouts/admin')

@section('Contents')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Konfirmasi Data Profil <small>Karyawan yang belum mengonfirmasi</small></h1>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Filter</h3>
            </div>
            <form method="GET" action="{{ url('/ProfileConfirmations') }}">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="periode">Periode</label>
                            <select class="form-control" id="periode" name="periode">
                                @foreach($periods as $availablePeriod)
                                    <option value="{{ $availablePeriod }}" @selected((string) $availablePeriod === $period)>
                                        {{ $availablePeriod }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="dept_code">Department</label>
                            <select class="form-control" id="dept_code" name="dept_code">
                                <option value="">Semua departemen</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->dept_code }}" @selected(($filters['dept_code'] ?? '') === (string) $department->dept_code)>
                                        {{ $department->dept_code }} - {{ $department->dept_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="position_name">Position</label>
                            <select class="form-control" id="position_name" name="position_name">
                                <option value="">Semua posisi</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position }}" @selected(($filters['position_name'] ?? '') === (string) $position)>
                                        {{ $position }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a href="{{ url('/ProfileConfirmations') }}" class="btn btn-default">Reset</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Terapkan filter
                    </button>
                </div>
            </form>
        </div>

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{ number_format($pendingEmployees->total()) }} karyawan belum konfirmasi periode {{ $period }}
                </h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>ID Employee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingEmployees as $employee)
                            <tr>
                                <td>{{ $pendingEmployees->firstItem() + $loop->index }}</td>
                                <td>{{ $employee->NIK }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>{{ $employee->dept_code ?? '-' }}{{ $employee->dept_name ? ' - '.$employee->dept_name : '' }}</td>
                                <td>{{ $employee->position_name ?? '-' }}</td>
                                <td>{{ $employee->id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Semua karyawan sudah mengonfirmasi untuk periode {{ $period }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                {{ $pendingEmployees->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </section>
</div>
@endsection
