@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Working Area <small>Create New Data</small></h1>
		</section>

		<section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Input</h3>
                        </div>
                        <form action="{{ url('/working_area') }}" method="POST">
                            @csrf
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="id_employee">Employee</label>
                                    <select class="form-control" name="id_employee" id="id_employee" required>
                                        <option value="">-- Select Employee --</option>
                                        @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->id }} - {{ $emp->employee_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_area">Area</label>
                                    <select class="form-control" name="id_area" id="id_area" required>
                                        <option value="">-- Select Area --</option>
                                        @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->id }} - {{ $area->area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{{ url('/working_area') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-primary pull-right">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</section>
  	</div>
@endsection
