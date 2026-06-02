@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Master Area <small>Edit Data</small></h1>
		</section>

		<section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Update</h3>
                        </div>
                        <form action="{{ url('/master_area/'.$data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="plant">Plant</label>
                                    <input type="text" class="form-control" name="plant" id="plant" value="{{ $data->plant }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" name="area" id="area" value="{{ $data->area }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="ap">Access Point (AP ID)</label>
                                    <input type="number" class="form-control" name="ap" id="ap" value="{{ $data->ap }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1" {{ $data->is_active == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $data->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{{ url('/master_area') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-warning pull-right">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</section>
  	</div>
@endsection
