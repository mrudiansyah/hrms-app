@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				Edit Role
				<small>Update system role</small>
			</h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-4">
				<div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Role Form</h3>
                    </div>
                    
                    <form action="{{ url('/role-management/'.$role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
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
                                <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Update</button>
                            <a href="{{ url('/role-management') }}" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
				</div>
			</div>
		</div>
		</section>
  	</div>
@endsection
