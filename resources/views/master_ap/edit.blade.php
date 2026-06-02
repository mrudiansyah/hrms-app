@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Master Access Point <small>Edit Data</small></h1>
		</section>

		<section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Update</h3>
                        </div>
                        <form action="{{ url('/master_ap/'.$data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="access_point">Assembly Point (Number)</label>
                                    <input type="number" class="form-control" name="access_point" id="access_point" value="{{ $data->access_point }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="area">Area Description</label>
                                    <input type="text" class="form-control" name="area" id="area" value="{{ $data->area }}" required>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{{ url('/master_ap') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-warning pull-right">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</section>
  	</div>
@endsection
