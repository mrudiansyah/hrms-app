@extends('layouts/admin')
@section('Contents')
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				Add User
				<small>Create new system user</small>
			</h1>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-12 col-md-6 col-lg-4">
				<div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">User Form</h3>
                    </div>
                    
                    <form action="{{ url('/user-management') }}" method="POST">
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
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter Name" required>
                            </div>
                            <div class="form-group">
                                <label>Email address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter email" required>
                            </div>

                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                            <a href="{{ url('/user-management') }}" class="btn btn-default">Cancel</a>
                        </div>
                    </form>
				</div>
			</div>
		</div>
		</section>
  	</div>
@endsection
