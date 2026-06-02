@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>Master Area <small>Create New Data</small></h1>
		</section>

		<section class="content">
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Input</h3>
                        </div>
                        <form action="{{ url('/master_area') }}" method="POST">
                            @csrf
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="plant">Plant</label>
                                    <input type="text" class="form-control" name="plant" id="plant" required>
                                </div>
                                <div class="form-group">
                                    <label for="area">Area</label>
                                    <input type="text" class="form-control" name="area" id="area" required>
                                </div>
                                <div class="form-group">
                                    <label for="ap">Access Point (AP ID)</label>
                                    <input type="number" class="form-control" name="ap" id="ap" required>
                                </div>
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{{ url('/master_area') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-primary pull-right">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
		</section>
  	</div>
@endsection
