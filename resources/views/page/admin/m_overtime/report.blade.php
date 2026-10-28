@extends('layouts/admin')

@section('Contents')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Overtime Report <small>Detail</small></h1>
            <ol class="breadcrumb">
                <li><a href="{{ url('/Dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Overtime Report</li>
            </ol>
        </section>

        <section class="content">
            <style>
                .overtime-filter {
                    border-top: 3px solid #0f766e;
                }

                .overtime-filter .box-header {
                    padding: 14px 18px;
                }

                .overtime-filter .box-title {
                    font-size: 16px;
                    font-weight: 600;
                }

                .overtime-filter .box-title i {
                    margin-right: 7px;
                    color: #0f766e;
                }

                .overtime-filter .box-body {
                    padding: 18px;
                    background: #f7fafb;
                }

                .overtime-filter-grid {
                    display: grid;
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                    gap: 16px;
                }

                .overtime-filter-field {
                    min-width: 0;
                }

                .overtime-filter-field label {
                    display: block;
                    margin-bottom: 6px;
                    color: #34495e;
                    font-size: 12px;
                    font-weight: 600;
                }

                .overtime-filter-field .form-control {
                    width: 100%;
                    height: 38px;
                    border-color: #d6e0e4;
                    border-radius: 4px;
                    box-shadow: none;
                    transition: border-color .15s ease, box-shadow .15s ease;
                }

                .overtime-filter-field .form-control:focus {
                    border-color: #0f766e;
                    box-shadow: 0 0 0 2px rgba(15, 118, 110, .12);
                }

                .overtime-filter-actions {
                    grid-column: 1 / -1;
                    display: flex;
                    justify-content: flex-end;
                    gap: 8px;
                    padding-top: 14px;
                    border-top: 1px solid #e3eaed;
                }

                .overtime-filter-actions .btn {
                    min-width: 106px;
                    height: 36px;
                    border-radius: 4px;
                }

                .overtime-filter-actions .btn-apply {
                    border-color: #0f766e;
                    background: #0f766e;
                    color: #fff;
                }

                .overtime-filter-actions .btn-apply:hover,
                .overtime-filter-actions .btn-apply:focus {
                    border-color: #115e59;
                    background: #115e59;
                    color: #fff;
                }

                @media (max-width: 991px) {
                    .overtime-filter-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                    }
                }

                @media (max-width: 540px) {
                    .overtime-filter .box-body {
                        padding: 14px;
                    }

                    .overtime-filter-grid {
                        grid-template-columns: minmax(0, 1fr);
                        gap: 12px;
                    }

                    .overtime-filter-actions {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                    }

                    .overtime-filter-actions .btn {
                        width: 100%;
                    }
                }
            </style>

            <div class="box box-default overtime-filter">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-sliders"></i> Report Filters</h3>
                </div>
                <div class="box-body">
                    <form action="{{ url('/Overtimes/Report') }}" method="GET">
                        <div class="overtime-filter-grid">
                            <div class="overtime-filter-field">
                            <label for="periode">OT Period</label>
                            <select name="periode" id="periode" class="form-control">
                                @foreach($periods as $periodOption)
                                    <option value="{{ $periodOption }}" {{ $period == $periodOption ? 'selected' : '' }}>{{ $periodOption }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($departments->count() > 1)
                            <div class="overtime-filter-field">
                                <label for="dept_id">Department</label>
                                <select name="dept_id" id="dept_id" class="form-control">
                                    <option value="">All authorized departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->dept_id }}" {{ (string) $departmentFilter === (string) $department->dept_id ? 'selected' : '' }}>
                                            {{ $department->dept_code }} - {{ $department->dept_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="overtime-filter-field">
                            <label for="category_ot">Category</label>
                            <select name="category_ot" id="category_ot" class="form-control">
                                <option value="">All categories</option>
                                @foreach($categories as $categoryOption)
                                    <option value="{{ $categoryOption }}" {{ $category === $categoryOption ? 'selected' : '' }}>{{ $categoryOption }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="overtime-filter-field">
                            <label for="detail_category">Detail</label>
                            <select name="detail_category" id="detail_category" class="form-control">
                                <option value="">All details</option>
                                @foreach($details as $detailOption)
                                    <option value="{{ $detailOption }}" {{ $detailCategory === $detailOption ? 'selected' : '' }}>{{ $detailOption }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="overtime-filter-field">
                            <label for="employee">Employee</label>
                            <select name="employee" id="employee" class="form-control">
                                <option value="">All employees</option>
                                @foreach($employees as $employeeOption)
                                    <option value="{{ $employeeOption->NIK }}" {{ $employee === (string) $employeeOption->NIK ? 'selected' : '' }}>
                                        {{ $employeeOption->NIK }} - {{ $employeeOption->employee_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="overtime-filter-field">
                            <label for="sort_by">Sort by</label>
                            <select name="sort_by" id="sort_by" class="form-control">
                                @foreach($sortOptions as $sortKey => $sortLabel)
                                    <option value="{{ $sortKey }}" {{ $sortBy === $sortKey ? 'selected' : '' }}>{{ $sortLabel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="overtime-filter-field">
                            <label for="direction">Order</label>
                            <select name="direction" id="direction" class="form-control">
                                <option value="desc" {{ $sortDirection === 'desc' ? 'selected' : '' }}>Descending</option>
                                <option value="asc" {{ $sortDirection === 'asc' ? 'selected' : '' }}>Ascending</option>
                            </select>
                        </div>

                            <div class="overtime-filter-actions">
                                <a href="{{ url('/Overtimes/Report') }}" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                                <button type="submit" class="btn btn-apply">
                                    <i class="fa fa-filter"></i> Apply filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-list"></i> Overtime Details</h3>
                    <div class="box-tools">
                        <span class="label label-default">{{ number_format($rows->total()) }} records</span>
                    </div>
                </div>
                <div class="box-body">
                    @if($departments->isEmpty())
                        <div class="alert alert-warning">No department access is assigned to your account.</div>
                    @endif
                    <div class="table-responsive">
                        <table id="overtime-report-table" class="table table-bordered table-striped table-hover table-condensed">
                            <thead>
                                <tr>
                                    @foreach($columns as $column)
                                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $row)
                                    <tr>
                                        @foreach($columns as $column)
                                            <td>{{ $column === 'ammount' && !$canSeeAmountDetails ? '-' : data_get($row, $column, '-') }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($columns) }}" class="text-center">No overtime data found for these filters.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="active">
                                    <th colspan="9">Total</th>
                                    <th>{{ number_format((float) $totalHours, 2) }}</th>
                                    <th>{{ number_format((float) $totalAmount, 0) }}</th>
                                    <th colspan="{{ count($columns) - 11 }}"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    {{ $rows->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection

@section('Scripts')
    <script>
        $(function () {
            $('#overtime-report-table').DataTable({
                paging: false,
                searching: false,
                ordering: false,
                info: false,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'excel', 'print']
            });
        });
    </script>
@endsection