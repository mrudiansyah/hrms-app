@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				Add Role
				<small>Create new system role</small>
			</h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-4">
				<div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Role Form</h3>
                    </div>
                    
                    <form action="{{ url('/role-management') }}" method="POST">
                        @csrf
                        <div class="box-body">
                            
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul style="margin-bottom:0;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label>Role Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter Role Name" required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                            <a href="{{ url('/role-management') }}" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
				</div>
			</div>
		</div>
		</section>
  	</div>
@endsection
