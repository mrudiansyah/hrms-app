@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Master Access Point <small>Create New Data</small></h1>
		</section>

		<section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Input</h3>
                        </div>
                        <form action="{{ url('/master_ap') }}" method="POST">
                            @csrf
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="access_point">Assembly Point (Number)</label>
                                    <input type="number" class="form-control" name="access_point" id="access_point" required>
                                </div>
                                <div class="form-group">
                                    <label for="area">Area Description</label>
                                    <input type="text" class="form-control" name="area" id="area" required>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{{ url('/master_ap') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-primary pull-right">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</section>
  	</div>
@endsection
